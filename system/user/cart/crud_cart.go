package cart

import (
	"encoding/base64"
	"encoding/json"
	"fmt"
	"log"
	"net/http"
	"strconv"

	"telkomgoods/config"
)

// CartItem represents an item in the cart
type CartItem struct {
	ProductID int `json:"productId"`
	OptionID  int `json:"optionId,omitempty"`
	Quantity  int `json:"quantity,omitempty"`
}

func AddToCartHandler(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Error(w, "Invalid request method", http.StatusMethodNotAllowed)
		return
	}

	userID, err := getUserIDFromSession(r)
	if err != nil {
		http.Error(w, "User not logged in", http.StatusUnauthorized)
		return
	}

	var item CartItem
	if err := json.NewDecoder(r.Body).Decode(&item); err != nil {
		http.Error(w, "Invalid request payload", http.StatusBadRequest)
		return
	}

	db, err := config.ConnectDB()
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()

	where := map[string]string{
		"user_id":    strconv.Itoa(userID),
		"product_id": strconv.Itoa(item.ProductID),
	}
	if item.OptionID != 0 {
		where["option_id"] = strconv.Itoa(item.OptionID)
	}

	existingCartItem, err := config.Fetch(db, "tb_keranjang", where)
	if err != nil {
		if err.Error() == "no rows found" {
			// Insert new item
			data := map[string]string{
				"user_id":    strconv.Itoa(userID),
				"product_id": strconv.Itoa(item.ProductID),
				"quantity":   "1",
			}
			if item.OptionID != 0 {
				data["option_id"] = strconv.Itoa(item.OptionID)
			}
			status := config.Insert(db, "tb_keranjang", data)
			if status != "OK" {
				http.Error(w, "Failed to insert cart item", http.StatusInternalServerError)
				return
			}
		} else {
			http.Error(w, "Failed to query cart", http.StatusInternalServerError)
			return
		}
	} else {
		// Update existing item quantity
		quantity, _ := strconv.Atoi(existingCartItem["quantity"])
		data := map[string]string{
			"quantity": strconv.Itoa(quantity + 1),
		}
		status := config.Update(db, "tb_keranjang", data, where)
		if status != "OK" {
			http.Error(w, "Failed to update cart item", http.StatusInternalServerError)
			return
		}
	}

	// Count total items in the cart
	cartCountQuery := `SELECT SUM(quantity) AS total_items FROM tb_keranjang WHERE user_id = $1`
	var cartCount int
	err = db.QueryRow(cartCountQuery, userID).Scan(&cartCount)
	if err != nil {
		http.Error(w, "Failed to count cart items", http.StatusInternalServerError)
		return
	}

	response := map[string]interface{}{
		"success":   true,
		"cartCount": cartCount,
	}
	json.NewEncoder(w).Encode(response)
}

func getUserIDFromSession(r *http.Request) (int, error) {
	cookie, err := r.Cookie("session-name")
	if err != nil {
		return 0, err
	}

	// Decode base64 session cookie
	sessionJSON, err := base64.StdEncoding.DecodeString(cookie.Value)
	if err != nil {
		return 0, fmt.Errorf("failed to decode session cookie: %v", err)
	}

	var sessionData map[string]string
	if err := json.Unmarshal(sessionJSON, &sessionData); err != nil {
		return 0, fmt.Errorf("failed to unmarshal session data: %v", err)
	}

	userID, err := strconv.Atoi(sessionData["user_id"])
	if err != nil {
		return 0, fmt.Errorf("invalid user ID in session")
	}
	return userID, nil
}

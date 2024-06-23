package whatsapp

import (
	"bytes"
	"encoding/json"
	"io"
	"log"
	"net/http"

	"telkomgoods/config"
)

func SendMessage(nomor string, msg string) (string, error) {
	db, err := config.ConnectDB()
	if err != nil {
		return "", err
	}
	defer db.Close()

	var apiWa string
	query := "SELECT api_wa FROM tb_settings WHERE id_setting = 1"
	err = db.QueryRow(query).Scan(&apiWa)
	if err != nil {
		return "", err
	}

	url := "https://api.fonnte.com/send"
	requestBody, err := json.Marshal(map[string]string{
		"target":  nomor,
		"message": msg,
	})
	if err != nil {
		return "", err
	}

	req, err := http.NewRequest("POST", url, bytes.NewBuffer(requestBody))
	if err != nil {
		return "", err
	}
	req.Header.Set("Authorization", apiWa)
	req.Header.Set("Content-Type", "application/json")

	client := &http.Client{}
	resp, err := client.Do(req)
	if err != nil {
		return "", err
	}
	defer resp.Body.Close()

	body, err := io.ReadAll(resp.Body)
	if err != nil {
		return "", err
	}

	// Print response status and body for debugging
	log.Printf("WhatsApp API Response Status: %s", resp.Status)
	log.Printf("WhatsApp API Response Body: %s", string(body))

	return string(body), nil
}

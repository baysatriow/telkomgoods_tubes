package config

import (
	"database/sql"
	"fmt"
	"log"
)

func Insert(db *sql.DB, table string, data map[string]string) (status string) {
	fields := ""
	values := ""
	for f, v := range data {
		fields += fmt.Sprintf(", %s", f)
		values += fmt.Sprintf(", '%s'", v)
	}
	query := fmt.Sprintf("INSERT INTO %s (%s) VALUES(%s)", table, fields[1:], values[1:])
	_, err := db.Exec(query)
	if err != nil {
		log.Fatal(err)
		return "NO"
	}
	return "OK"
}

func Update(db *sql.DB, table string, data map[string]string, where map[string]string) (status string) {
	set := ""
	condition := ""
	for f, v := range data {
		set += fmt.Sprintf(", %s='%s'", f, v)
	}
	for f, v := range where {
		condition += fmt.Sprintf(" AND %s='%s'", f, v)
	}
	query := fmt.Sprintf("UPDATE %s SET %s WHERE %s", table, set[1:], condition[4:])
	_, err := db.Exec(query)
	if err != nil {
		log.Fatal(err)
		return "NO"
	}
	return "OK"
}

func Delete(db *sql.DB, table string, where map[string]string) (status string) {
	query := "DELETE FROM " + table
	if where != nil {
		condition := ""
		for f, v := range where {
			condition += fmt.Sprintf(" AND %s='%s'", f, v)
		}
		query += " WHERE " + condition[4:]
	}
	_, err := db.Exec(query)
	if err != nil {
		log.Fatal(err)
		return "NO"
	}
	return "OK"
}

func Fetch(db *sql.DB, table string, where map[string]string) (result map[string]string, err error) {
	query := "SELECT * FROM " + table
	if where != nil {
		condition := ""
		for f, v := range where {
			condition += fmt.Sprintf(" AND %s='%s'", f, v)
		}
		query += " WHERE " + condition[4:]
	}
	rows, err := db.Query(query)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	// Retrieve column names
	columns, err := rows.Columns()
	if err != nil {
		return nil, err
	}

	// Assume only one row is returned, otherwise handle it appropriately
	if rows.Next() {
		values := make([]interface{}, len(columns))
		for i := range values {
			values[i] = new(sql.RawBytes)
		}
		err = rows.Scan(values...)
		if err != nil {
			return nil, err
		}

		result = make(map[string]string)
		for i, colName := range columns {
			result[colName] = string(*values[i].(*sql.RawBytes))
		}
	} else {
		return nil, fmt.Errorf("no rows found")
	}

	return result, nil
}

func Select(db *sql.DB, table string, where map[string]string, order string, limit int) (results []map[string]string) {
	query := "SELECT * FROM " + table
	if where != nil {
		condition := ""
		for f, v := range where {
			condition += fmt.Sprintf(" AND %s='%s'", f, v)
		}
		query += " WHERE " + condition[4:]
	}
	if order != "" {
		query += " ORDER BY " + order
	}
	if limit != 0 {
		query += fmt.Sprintf(" LIMIT %d", limit)
	}
	rows, err := db.Query(query)
	if err != nil {
		log.Fatal(err)
	}
	defer rows.Close()
	columns, err := rows.Columns()
	if err != nil {
		log.Fatal(err)
	}
	values := make([]interface{}, len(columns))
	for i := range values {
		values[i] = new(sql.RawBytes)
	}
	for rows.Next() {
		err = rows.Scan(values...)
		if err != nil {
			log.Fatal(err)
		}
		result := make(map[string]string)
		for i, colName := range columns {
			result[colName] = string(*values[i].(*sql.RawBytes))
		}
		results = append(results, result)
	}
	if err = rows.Err(); err != nil {
		log.Fatal(err)
	}
	return results
}

func Rowcount(db *sql.DB, table string, where map[string]string) (count int) {
	query := "SELECT COUNT(*) FROM " + table
	if where != nil {
		condition := ""
		for f, v := range where {
			condition += fmt.Sprintf(" AND %s='%s'", f, v)
		}
		query += " WHERE " + condition[4:]
	}
	err := db.QueryRow(query).Scan(&count)
	if err != nil {
		log.Fatal(err)
	}
	return count
}

func Truncate(db *sql.DB, table string) (status string) {
	query := "TRUNCATE " + table
	_, err := db.Exec(query)
	if err != nil {
		log.Fatal(err)
		return "NO"
	}
	return "OK"
}

func main() {

}

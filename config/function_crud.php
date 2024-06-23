<?php    
    function insert($conn, $table, $data=null) {
        $command = 'INSERT INTO '.$table;
        $field = $value = null;
        foreach($data as $f => $v) {
            $field .= ','.$f;
            $value .= ", '".$v."'";
        }
        $command .= ' ('.substr($field,1).')';
        $command .= ' VALUES('.substr($value,1).')';
        $exec = pg_query($conn, $command);
        ($exec) ? $status = 'OK' : $status = 'NO';
        return $status;
    }
    
    function update($conn, $table, $data=null, $where=null) {
        $command = 'UPDATE '.$table.' SET ';
        $field = $value = null;
        foreach($data as $f => $v) {
            $field .= ",".$f."='".$v."'";
        }
        $command .= substr($field,1);
        if($where!=null) {
            foreach($where as $f => $v) {
                $value .= "#".$f."='".$v."'";
            }
            $command .= ' WHERE '.substr($value,1);
            $command = str_replace('#',' AND ',$command);
        }
        $exec = pg_query($conn, $command);
        ($exec) ? $status = 'OK' : $status = 'NO';
        return $status;
    }
    
    function delete($conn, $table, $where=null) {
        $command = 'DELETE FROM '.$table;
        if($where!=null) {
            $value = null;
            foreach($where as $f => $v) {
                $value .= "#".$f."='".$v."'";
            }
            $command .= ' WHERE '.substr($value,1);
            $command = str_replace('#',' AND ',$command);
        }
        $exec = pg_query($conn, $command);
        ($exec) ? $status = 'OK' : $status = 'NO';
        return $status;
    }
    
    function fetch($conn, $table, $where=null) {
        $command = 'SELECT * FROM '.$table;
        if($where!=null) {
            $value = null;
            foreach($where as $f => $v) {
                $value .= "#".$f."='".$v."'";
            }
            $command .= ' WHERE '.substr($value,1);
            $command = str_replace('#',' AND ',$command);
        }
        $sql = pg_query($conn, $command);
        $exec = pg_fetch_assoc($sql);
        return $exec;
    }
    
    function select($conn, $table, $where=null, $order=null, $limit=null) {
        $command = 'SELECT * FROM '.$table;
        if($where!=null) {
            $value = null;
            foreach($where as $f => $v) {
                $value .= "#".$f."='".$v."'";
            }
            $command .= ' WHERE '.substr($value,1);
            $command = str_replace('#',' AND ',$command);
        }
        ($order!=null) ? $command .= ' ORDER BY '.$order :null;
        ($limit!=null) ? $command .= ' LIMIT '.$limit :null;
        $result = array();
        $sql = pg_query($conn, $command);
        while($field = pg_fetch_assoc($sql)) {
            $result[] = $field;
        }
        return $result;
    }
    
    function rowcount($conn, $table, $where=null) {
        $command = 'SELECT COUNT(*) FROM '.$table;
        if($where!=null) {
            $value = null;
            foreach($where as $f => $v) {
                $value .= "#".$f."='".$v."'";
            }
            $command .= ' WHERE '.substr($value,1);
            $command = str_replace('#',' AND ',$command);
        }
        $exec = pg_query($conn, $command);
        $row = pg_fetch_row($exec);
        return $row[0];
    }
    
    function truncate($conn, $table) {
        $command = 'TRUNCATE '.$table;
        $exec = pg_query($conn, $command);
        ($exec) ? $status = 'OK' : $status = 'NO';
        return $status;
    }
?>

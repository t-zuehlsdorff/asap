[INSERT-PORT]
QUERY = "INSERT INTO ports (category, port, version)
         VALUES (?, ?, ?)
         RETURNING portid"
HANDLER = SINGLE_VALUE

START TRANSACTION;

CREATE TABLE ports (
  portid   SERIAL NOT NULL PRIMARY KEY,
  category TEXT   NOT NULL,
  port     TEXT   NOT NULL,
  version  TEXT   NOT NULL,
  UNIQUE (category, port)
);

CREATE INDEX ON ports (port);

CREATE TABLE port_checks (
  checkid    BIGSERIAL   NOT NULL PRIMARY KEY,
  portid     INT         NOT NULL REFERENCES ports (portid) ON UPDATE CASCADE ON DELETE CASCADE,
  check_time TIMESTAMPTZ NOT NULL DEFAULT current_timestamp
);

CREATE INDEX ON port_checks (portid, check_time DESC);

CREATE TABLE found_port_updates (
  checkid     BIGINT NOT NULL REFERENCES port_checks (checkid) ON UPDATE CASCADE ON DELETE CASCADE,
  new_version TEXT   NOT NULL
);
  
COMMIT;

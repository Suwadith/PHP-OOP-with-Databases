CREATE TABLE bicycle
(
  id           INT(11) AUTO_INCREMENT PRIMARY KEY,
  brand        VARCHAR(255) NOT NULL,
  model        VARCHAR(255) NOT NULL,
  year         INT(4) NOT NULL,
  category     VARCHAR(255) NOT NULL,
  gender       VARCHAR(255) NOT NULL,
  color        VARCHAR(255) NOT NULL,
  price        DECIMAL(9,2) NOT NULL,
  weight_kg    DECIMAL(9,5) NOT NULL,
  condition_id TINYINT(3) NOT NULL,
  description  TEXT         NOT NULL
);
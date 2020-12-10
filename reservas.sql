DROP TABLE IF EXISTS usuarios CASCADE;

CREATE TABLE usuarios
(
    id       bigserial    PRIMARY KEY
  , login    varchar(255) NOT NULL UNIQUE
  , password varchar(255) NOT NULL
);

DROP TABLE IF EXISTS pistas CASCADE;

CREATE TABLE pistas
(
    id           bigserial    PRIMARY KEY
  , denominacion varchar(255) NOT NULL
);

DROP TABLE IF EXISTS reservas CASCADE;

CREATE TABLE reservas
(
    id         bigserial    PRIMARY KEY
  , pista_id   bigint       NOT NULL REFERENCES pistas (id)
  , fecha_hora timestamp(0) NOT NULL
  , usuario_id bigint       NOT NULL REFERENCES usuarios (id)
  , UNIQUE (pista_id, fecha_hora)
);

INSERT INTO usuarios (login, password)
VALUES ('pepe', crypt('pepe', gen_salt('bf', 10)))
     , ('juan', crypt('juan', gen_salt('bf', 10)));

INSERT INTO pistas (denominacion)
VALUES ('Picacho')
     , ('Polideportivo')
     , ('Dehesilla');

INSERT INTO reservas (pista_id, fecha_hora, usuario_id)
VALUES (1, '2020-12-16 15:00:00', 1);
CREATE DATABASE celestial_barbearia;

USE celestial_barbearia;

CREATE TABLE agendamentos (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    data DATE NOT NULL,
    horario TIME NOT NULL,
    servico VARCHAR(255) NOT NULL,
    barbeiro VARCHAR(255) NOT NULL,
    mensagem TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);


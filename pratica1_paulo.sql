CREATE DATABASE pratica1_paulo;
USE pratica1_paulo;

CREATE TABLE clientes(
id_cliente INT PRIMARY KEY AUTO_INCREMENT,
nome_cliente VARCHAR(100) NOT NULL,
email_cliente VARCHAR(100) NOT NULL,
telefone_cliente VARCHAR(9) NOT NULL
);

CREATE TABLE chamados(
id_chamado INT PRIMARY KEY AUTO_INCREMENT,
fk_cliente INT NOT NULL,
descricao_chamado VARCHAR(1000) NOT NULL,
criticidade_chamado ENUM ("baixa", "média", "alta") NOT NULL,
status_chamado ENUM ("aberto", "em andamento", "resolvido") NOT NULL,
data_chamado timestamp default current_timestamp
);
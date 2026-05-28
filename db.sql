CREATE DATABASE kasir_db;
USE kasir_db;

CREATE TABLE barang (
    id_barang INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(100),
    harga INT,
    stok INT,
    tipe VARCHAR(50)
) AUTO_INCREMENT=1;

CREATE TABLE transaksi (
    id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
    tgl_transaksi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total INT,
    nama_pemesan VARCHAR(150)
) AUTO_INCREMENT=1;


CREATE TABLE detail_transaksi (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_transaksi INT,
    id_barang INT,
    jumlah INT,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi),
    FOREIGN KEY (id_barang) REFERENCES barang(id_barang)
) AUTO_INCREMENT=1;

CREATE TABLE stok (
    id_stok INT AUTO_INCREMENT PRIMARY KEY,
    nama_stok VARCHAR(100),
    stok INT
) AUTO_INCREMENT=1;

-- Contoh data barang per tipe untuk transaksi (mocktail, milk base, coffe, snack)
INSERT INTO barang (id_barang, nama_barang, harga, stok, tipe) VALUES
    (1, 'Blue Ocean', 15000, 50, 'mocktail'),
    (2, 'Mocktail A', 20000, 50, 'mocktail'),
    (3, 'Mocktail B', 25000, 50, 'mocktail'),
    (4, 'Milk Base A', 18000, 50, 'milk base'),
    (5, 'Milk Base B', 22000, 50, 'milk base'),
    (6, 'Coffe A', 25000, 50, 'coffe'),
    (7, 'Coffe B', 30000, 50, 'coffe'),
    (8, 'Snack A', 12000, 100, 'snack'),
    (9, 'Snack B', 15000, 100, 'snack');

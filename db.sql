CREATE DATABASE kasir_db;
USE kasir_db;

CREATE TABLE barang (
    id_barang INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(100),
    harga INT,
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
INSERT INTO barang (id_barang, nama_barang, harga, tipe) VALUES
    (1, 'Cranora', 18000, 'mocktail'),
    (2, 'Solvia', 18000, 'mocktail'),
    (3, 'Brezza', 18000, 'mocktail'),
    (4, 'Matcha', 18000, 'milk base'),
    (5, 'Chocolate', 18000, 'milk base'),
    (6, 'Red Velvet', 18000, 'milk base'),
    (7, 'Milky Berry', 18000, 'milk base'),
    (8, 'Tubruk', 12000, 'coffe'),
    (9, 'Berryboo', 18000, 'coffe'),
    (10, 'Moora', 16000, 'coffe'),
    (11, 'Americano', 15000, 'coffe'),
    (12, 'caffra', 16000, 'coffe'),
    (13, 'Cheese Roll', 15000, 'snack'),
    (14, 'BBQ French Fries', 15000, 'snack'),
    (15, 'Mix Platter', 18000, 'snack');


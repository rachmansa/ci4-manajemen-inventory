-- melihat jumlah stok laptop yang tersedia
SELECT b.id_barang, b.nama_barang, COUNT(bd.id_barang_detail) AS stok
FROM barang b
LEFT JOIN barang_detail bd ON b.id_barang = bd.id_barang AND bd.status = 'tersedia'
GROUP BY b.id_barang;

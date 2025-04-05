START TRANSACTION;

CREATE TABLE `classification_periodique` (
  `id` int(12) NOT NULL,
  `symbole` varchar(256) NOT NULL,
  `nom` varchar(256) NOT NULL,
  `numero` int(12) NOT NULL,
  `masse_atomique` float NOT NULL,
  `electronegativite` float DEFAULT NULL,
  `ligne` int(12) NOT NULL,
  `colonne` int(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `classification_periodique` (`id`, `symbole`, `nom`, `numero`, `masse_atomique`, `electronegativite`, `ligne`, `colonne`) VALUES
(1, 'H', 'Hydrogène', 1, 1.008, 2.2, 1, 1),
(2, 'He', 'Hélium', 2, 4.0026, NULL, 1, 18),
(3, 'Li', 'Lithium', 3, 6.94, 0.98, 2, 1),
(4, 'Be', 'Bérylium', 4, 9.0122, 1.57, 2, 2),
(5, 'B', 'Bore', 5, 10.81, 2.04, 2, 13),
(6, 'C', 'Carbone', 6, 12.011, 2.55, 2, 14),
(7, 'N', 'Azote', 7, 14.007, 3.04, 2, 15),
(8, 'O', 'Oxygène', 8, 15.999, 3.44, 2, 16),
(9, 'F', 'Fluor', 9, 18.998, 3.98, 2, 17),
(10, 'Ne', 'Néon', 10, 20.18, NULL, 2, 18),
(11, 'Na', 'Sodium', 11, 22.99, 0.93, 3, 1),
(12, 'Mg', 'Magnésium', 12, 24.305, 1.31, 3, 2),
(13, 'Al', 'aluminium', 13, 26.982, 1.61, 3, 13),
(14, 'Si', 'Silicium', 14, 28.085, 1.9, 3, 14),
(15, 'P', 'Phosphore', 15, 30.974, 2.19, 3, 15),
(16, 'S', 'Soufre', 16, 32.06, 2.58, 3, 16),
(17, 'Cl', 'Clore', 17, 35.45, 3.16, 3, 17),
(18, 'Ar', 'Argon', 18, 39.948, NULL, 3, 18);

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `mail` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);
COMMIT;
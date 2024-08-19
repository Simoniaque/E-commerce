-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 19 août 2024 à 04:00
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `newvet`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `nom`) VALUES
(1, 'Robes'),
(2, 'Hauts'),
(3, 'Pantalons'),
(4, 'Chaussures'),
(5, 'Accessoires');

-- --------------------------------------------------------

--
-- Structure de la table `categories_en_avant`
--

CREATE TABLE `categories_en_avant` (
  `id` int(11) NOT NULL,
  `categorie_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories_en_avant`
--

INSERT INTO `categories_en_avant` (`id`, `categorie_id`) VALUES
(3, 1),
(2, 2),
(4, 3),
(1, 4);

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `date_creation` date NOT NULL DEFAULT current_timestamp(),
  `prix_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `statut` int(1) NOT NULL DEFAULT 1,
  `chiffres_cb` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id`, `utilisateur_id`, `date_creation`, `prix_total`, `statut`, `chiffres_cb`) VALUES
(1, 1, '2024-06-21', 119.98, 4, 1234),
(2, 2, '2024-06-21', 89.97, 2, 5678),
(3, 1, '2024-08-22', 219.96, 3, 9123),
(4, 2, '2024-08-23', 299.97, 2, 4567),
(5, 3, '2024-08-24', 79.98, 4, 8912),
(6, 1, '2024-08-25', 159.98, 1, 3456),
(7, 2, '2024-08-26', 49.99, 4, 7891),
(8, 3, '2024-08-27', 249.95, 3, 2345),
(9, 1, '2024-08-28', 399.95, 2, 6789);

-- --------------------------------------------------------

--
-- Structure de la table `details_commandes`
--

CREATE TABLE `details_commandes` (
  `id` int(11) NOT NULL,
  `commande_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `quantite` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `details_commandes`
--

INSERT INTO `details_commandes` (`id`, `commande_id`, `produit_id`, `quantite`) VALUES
(1, 1, 1, 2),
(2, 2, 3, 3),
(3, 3, 1, 1),
(4, 3, 2, 1),
(5, 3, 4, 2),
(6, 4, 3, 2),
(7, 4, 7, 1),
(9, 5, 5, 1),
(10, 5, 6, 1),
(11, 5, 10, 2),
(12, 6, 2, 1),
(13, 6, 4, 1),
(14, 6, 7, 2),
(15, 7, 3, 1),
(17, 8, 1, 2),
(18, 8, 6, 1),
(19, 8, 10, 1),
(20, 9, 5, 2),
(21, 9, 7, 1);

-- --------------------------------------------------------

--
-- Structure de la table `details_panier`
--

CREATE TABLE `details_panier` (
  `id` int(11) NOT NULL,
  `panier_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `quantite` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `details_panier`
--

INSERT INTO `details_panier` (`id`, `panier_id`, `produit_id`, `quantite`) VALUES
(1, 1, 1, 2),
(2, 1, 2, 1),
(3, 1, 3, 1),
(4, 2, 3, 3),
(5, 2, 4, 1),
(6, 2, 5, 2),
(7, 3, 6, 1),
(8, 3, 7, 1),
(9, 3, 10, 2);

-- --------------------------------------------------------

--
-- Structure de la table `infos_clients`
--

CREATE TABLE `infos_clients` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `code_postal` varchar(20) DEFAULT NULL,
  `pays` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `infos_clients`
--

INSERT INTO `infos_clients` (`id`, `utilisateur_id`, `adresse`, `ville`, `code_postal`, `pays`, `telephone`) VALUES
(1, 1, '10 Rue de la Mode', 'Paris', '75001', 'France', '0123456789'),
(2, 2, '25 Avenue des Roses', 'Lyon', '69002', 'France', '0987654321'),
(3, 3, '5 Boulevard Chic', 'Marseille', '13003', 'France', '0147258369');

-- --------------------------------------------------------

--
-- Structure de la table `paniers`
--

CREATE TABLE `paniers` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `paniers`
--

INSERT INTO `paniers` (`id`, `utilisateur_id`) VALUES
(1, 1),
(2, 2),
(3, 3);

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id` int(11) NOT NULL,
  `categorie_id` int(11) DEFAULT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `materiaux` varchar(50) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `date_ajout` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id`, `categorie_id`, `nom`, `description`, `materiaux`, `prix`, `stock`, `date_ajout`) VALUES
(1, 1, 'Robe Midi Élégante', 'Robe midi élégante pour toutes les occasions.', 'Soie', 59.99, 50, '2024-08-13'),
(2, 1, 'Robe Maxi Florale', 'Robe longue avec motif floral, parfaite pour l\'été.', 'Soie', 79.99, 30, '2024-08-09'),
(3, 2, 'Chemisier à Manches Longues', 'Chemisier féminin à manches longues.', 'Soie', 29.99, 80, '2024-04-24'),
(4, 2, 'Débardeur Basique', 'Débardeur confortable pour une tenue décontractée.', 'Coton', 14.99, 100, '2024-02-16'),
(5, 3, 'Jean Skinny Stretch', 'Jean stretch et ajusté pour un look moderne.', 'Jean', 49.99, 0, '2024-08-21'),
(6, 4, 'Bottes Hautes', 'Bottes élégantes pour l\'hiver.', 'Cuire', 89.99, 40, '2024-08-28'),
(7, 4, 'Sandales à Talons', 'Sandales à talons pour les soirées.', 'Liège et cuir', 69.99, 60, '2024-01-10'),
(10, 4, 'Chaussure à talon', 'Chaussure à talon en cuir', 'Cuir', 49.00, 20, '2018-08-15');

-- --------------------------------------------------------

--
-- Structure de la table `produits_en_avant`
--

CREATE TABLE `produits_en_avant` (
  `id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produits_en_avant`
--

INSERT INTO `produits_en_avant` (`id`, `produit_id`) VALUES
(4, 1),
(2, 5),
(1, 6),
(3, 10);

-- --------------------------------------------------------

--
-- Structure de la table `tokens_verification_mail`
--

CREATE TABLE `tokens_verification_mail` (
  `utilisateur_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `date_max` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `tokens_verification_mail`
--

INSERT INTO `tokens_verification_mail` (`utilisateur_id`, `token`, `date_max`) VALUES
(1, 'token123456', '2024-12-31 23:59:59'),
(2, 'token789012', '2024-12-31 23:59:59'),
(3, 'token345678', '2024-12-31 23:59:59'),
(4, 'token901234', '2024-12-31 23:59:59');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `est_admin` tinyint(1) DEFAULT 0,
  `mail_verifie` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `email`, `mot_de_passe`, `est_admin`, `mail_verifie`) VALUES
(1, 'Emma Martin', 'emma@example.com', 'motdepasse1', 0, 0),
(2, 'Laura Dubois', 'laura@example.com', 'motdepasse2', 0, 0),
(3, 'Sophie Lefevre', 'sophie@example.com', 'motdepasse3', 0, 0),
(4, 'Admin Shop', 'admin@example.com', 'admin123', 1, 0),
(9, 'Test', 'tsuyoki127@hotmail.com', 'test', 0, 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `categories_en_avant`
--
ALTER TABLE `categories_en_avant`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_categorie` (`categorie_id`);

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `details_commandes`
--
ALTER TABLE `details_commandes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `commande_id` (`commande_id`),
  ADD KEY `produit_id` (`produit_id`);

--
-- Index pour la table `details_panier`
--
ALTER TABLE `details_panier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `panier_id` (`panier_id`),
  ADD KEY `produit_id` (`produit_id`);

--
-- Index pour la table `infos_clients`
--
ALTER TABLE `infos_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `paniers`
--
ALTER TABLE `paniers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categorie_id` (`categorie_id`);

--
-- Index pour la table `produits_en_avant`
--
ALTER TABLE `produits_en_avant`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_produit` (`produit_id`);

--
-- Index pour la table `tokens_verification_mail`
--
ALTER TABLE `tokens_verification_mail`
  ADD PRIMARY KEY (`utilisateur_id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `categories_en_avant`
--
ALTER TABLE `categories_en_avant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `details_commandes`
--
ALTER TABLE `details_commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `details_panier`
--
ALTER TABLE `details_panier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `infos_clients`
--
ALTER TABLE `infos_clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `paniers`
--
ALTER TABLE `paniers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `produits_en_avant`
--
ALTER TABLE `produits_en_avant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `categories_en_avant`
--
ALTER TABLE `categories_en_avant`
  ADD CONSTRAINT `categories_en_avant_fk` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`);

--
-- Contraintes pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `commandes_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `details_commandes`
--
ALTER TABLE `details_commandes`
  ADD CONSTRAINT `details_commandes_ibfk_1` FOREIGN KEY (`commande_id`) REFERENCES `commandes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `details_commandes_ibfk_2` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`);

--
-- Contraintes pour la table `details_panier`
--
ALTER TABLE `details_panier`
  ADD CONSTRAINT `details_panier_ibfk_1` FOREIGN KEY (`panier_id`) REFERENCES `paniers` (`id`),
  ADD CONSTRAINT `details_panier_ibfk_2` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`);

--
-- Contraintes pour la table `infos_clients`
--
ALTER TABLE `infos_clients`
  ADD CONSTRAINT `infos_clients_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `paniers`
--
ALTER TABLE `paniers`
  ADD CONSTRAINT `paniers_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`);

--
-- Contraintes pour la table `produits`
--
ALTER TABLE `produits`
  ADD CONSTRAINT `produits_ibfk_1` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `produits_en_avant`
--
ALTER TABLE `produits_en_avant`
  ADD CONSTRAINT `produits_en_avant_fk` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`);

--
-- Contraintes pour la table `tokens_verification_mail`
--
ALTER TABLE `tokens_verification_mail`
  ADD CONSTRAINT `tokens_verification_mail_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 29 août 2024 à 01:23
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
-- Structure de la table `adresses_utilisateurs`
--

CREATE TABLE `adresses_utilisateurs` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `adresse_complète` varchar(255) NOT NULL,
  `ville` varchar(100) NOT NULL,
  `code_postal` varchar(20) NOT NULL,
  `pays` varchar(100) NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `adresses_utilisateurs`
--

INSERT INTO `adresses_utilisateurs` (`id`, `utilisateur_id`, `adresse_complète`, `ville`, `code_postal`, `pays`, `date_creation`) VALUES
(1, 19, '11 rue pierre cot', 'toulouse', '31200', 'france', '2024-08-25 15:38:29'),
(2, 19, 'tttttt', 'tt', 'ttt', 'ttt', '2024-08-25 16:03:47'),
(3, 19, 'aa', 'a', 'aa', 'aa', '2024-08-25 18:21:04');

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `nom`, `description`) VALUES
(1, 'Robes', 'Découvrez notre collection de robes qui combine élégance, confort et tendance. Que vous cherchiez une robe fluide pour l\'été, une robe de soirée chic ou une robe décontractée pour le quotidien, nous avons le modèle parfait pour chaque occasion'),
(2, 'Hauts', 'Explorez notre sélection de hauts, conçus pour s\'adapter à toutes vos envies et à chaque moment de la journée. Du t-shirt basique au chemisier raffiné, en passant par les blouses et les débardeurs, chaque pièce est pensée pour offrir confort et élégance. '),
(3, 'Pantalons', 'Nos pantalons allient style et fonctionnalité pour vous offrir une allure impeccable. Que vous préfériez les coupes ajustées, les pantalons larges ou les modèles plus casual, notre collection répondra à toutes vos attentes. Fabriqués avec des matériaux de'),
(4, 'Chaussures', 'Complétez votre tenue avec notre gamme de chaussures, alliant confort et style pour chaque pas. Des baskets tendances aux escarpins élégants, en passant par les sandales d\'été et les bottines hivernales, trouvez la paire qui correspond à votre style et à '),
(5, 'Accessoires', 'Les accessoires sont la touche finale qui sublime votre look. Explorez notre collection de sacs, bijoux, ceintures, et autres petits trésors pour personnaliser vos tenues avec style. Que vous cherchiez une pièce discrète ou un accessoire statement, notre '),
(9, 'Pulls', 'Découvrez notre collection de pulls, conçus pour vous offrir chaleur et style. Des classiques intemporels aux modèles tendance, nos pulls ajoutent une touche de confort à vos tenues tout au long de l’année. Trouvez votre pièce idéale pour allier douceur e'),
(10, 'Vestes', 'Alliant élégance et praticité, nos modèles vous assurent un look impeccable et un confort optimal. Trouvez la veste parfaite pour compléter votre tenue et affronter chaque journée avec style.');

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
(183, 2),
(184, 4),
(185, 5),
(186, 10);

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `date_creation` date NOT NULL DEFAULT current_timestamp(),
  `prix_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `statut` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id`, `utilisateur_id`, `date_creation`, `prix_total`, `statut`) VALUES
(1, 1, '2024-06-21', 119.98, 4),
(2, 2, '2024-06-21', 89.97, 2),
(3, 1, '2024-08-22', 219.96, 3),
(4, 2, '2024-08-23', 299.97, 2),
(5, 3, '2024-08-24', 79.98, 4),
(6, 1, '2024-08-25', 159.98, 1),
(7, 2, '2024-08-26', 49.99, 4),
(8, 3, '2024-08-27', 249.95, 3),
(9, 1, '2024-08-28', 399.95, 2);

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
-- Structure de la table `details_paniers`
--

CREATE TABLE `details_paniers` (
  `id` int(11) NOT NULL,
  `panier_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `quantite` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `details_paniers`
--

INSERT INTO `details_paniers` (`id`, `panier_id`, `produit_id`, `quantite`) VALUES
(28, 11, 2, 1);

-- --------------------------------------------------------

--
-- Structure de la table `materiaux`
--

CREATE TABLE `materiaux` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `materiaux`
--

INSERT INTO `materiaux` (`id`, `nom`) VALUES
(1, 'Coton'),
(2, 'Polyester'),
(3, 'Cuir'),
(4, 'Laine'),
(5, 'Soie');

-- --------------------------------------------------------

--
-- Structure de la table `messages_contact`
--

CREATE TABLE `messages_contact` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `sujet` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `date_message` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `moyens_paiement`
--

CREATE TABLE `moyens_paiement` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `numero_carte` varchar(20) DEFAULT NULL,
  `nom_titulaire` varchar(100) DEFAULT NULL,
  `date_expiration` date DEFAULT NULL,
  `cvv` varchar(4) DEFAULT NULL,
  `paypal_email` varchar(100) DEFAULT NULL,
  `date_ajout` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `moyens_paiement`
--

INSERT INTO `moyens_paiement` (`id`, `utilisateur_id`, `type`, `numero_carte`, `nom_titulaire`, `date_expiration`, `cvv`, `paypal_email`, `date_ajout`) VALUES
(1, 19, 'paypal', '', '', '0000-00-00', '', 'simon.auriac@limayrac.fr', '2024-08-25 18:25:07'),
(2, 19, 'card', '00', 'auriac simon', '0000-00-00', '000', '', '2024-08-25 18:26:06');

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
(11, 1);

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id` int(11) NOT NULL,
  `categorie_id` int(11) DEFAULT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `date_ajout` date NOT NULL DEFAULT current_timestamp(),
  `en_priorite` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id`, `categorie_id`, `nom`, `description`, `prix`, `stock`, `date_ajout`, `en_priorite`) VALUES
(1, 1, 'Robe Zébrée', 'Robe zébrée élégante pour toutes les occasion', 40.00, 30, '2024-08-13', 0),
(2, 1, 'Robe Maxi Florale', 'Robe longue avec motif floral, parfaite pour l&#039;été.', 79.99, 30, '2024-08-09', 0),
(3, 2, 'Chemisier à Manches Longues', 'Chemisier féminin à manches longues.', 29.99, 80, '2024-04-24', 0),
(4, 2, 'Débardeur Basique', 'Débardeur confortable pour une tenue décontractée.', 14.99, 100, '2024-02-16', 0),
(5, 3, 'Jean Skinny Stretch', 'Jean stretch et ajusté pour un look moderne.', 49.99, 0, '2024-08-21', 0),
(6, 4, 'Bottes Hautes', 'Bottes élégantes pour l&#039;hiver.', 89.99, 0, '2024-08-28', 1),
(7, 4, 'Sandales à Talons', 'Sandales à talons pour les soirées.', 69.99, 20, '2024-01-10', 1),
(10, 4, 'Chaussure à talon', 'Chaussure à talon en cuir', 49.00, 20, '2018-08-15', 0),
(11, 3, 'Pantalon Onlraffy-Yo Life', 'Ce produit est fabriqué à partir de polyester recyclé. Le polyester recyclé préserve les ressources naturelles et réduit la quantité de déchets.', 39.99, 50, '2024-08-20', 0),
(13, 5, 'Sac paille', 'Sac rond en paille avec anses dorées', 25.00, 50, '2024-08-26', 0),
(22, 3, 'Pantallon Habillé', 'Pantalon fluide à jambes larges, taille réglable avec liens ton sur ton et poches sur les côtés', 35.00, 50, '2024-08-26', 0),
(28, 5, ' Lunette de Soleil Polarisées', 'Protection UV400 Surdimensionnées Lunettes Classique Grand Cadre Lunettes de Soleil Femmes B2289', 19.99, 100, '2024-08-26', 0),
(32, 1, 'Pull noir en coton', 'Pull noir confortable et chaud', 35.00, 50, '2024-08-28', 0);

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
(130, 3),
(131, 5),
(132, 6),
(129, 22);

-- --------------------------------------------------------

--
-- Structure de la table `produits_materiaux`
--

CREATE TABLE `produits_materiaux` (
  `id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `materiau_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produits_materiaux`
--

INSERT INTO `produits_materiaux` (`id`, `produit_id`, `materiau_id`) VALUES
(202, 1, 1),
(203, 1, 2),
(103, 2, 2),
(93, 3, 2),
(94, 4, 1),
(95, 5, 1),
(96, 5, 3),
(98, 6, 3),
(97, 7, 2),
(99, 10, 3),
(100, 11, 2),
(35, 13, 2),
(53, 22, 1),
(54, 22, 2),
(110, 28, 2),
(208, 32, 1);

-- --------------------------------------------------------

--
-- Structure de la table `tokens_reinitialisation_mdp`
--

CREATE TABLE `tokens_reinitialisation_mdp` (
  `utilisateur_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `date_max` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tokens_verification_mail`
--

CREATE TABLE `tokens_verification_mail` (
  `utilisateur_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `date_max` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'Emma martin', 'emma@example.com', '$2y$10$MNayNfh78UrmFgvGR2noreUtwYUQ3kOh7QUh3AoG1MEhQvue/pcUi', 0, 1),
(2, 'Laura Dubois', 'laura@example.com', 'motdepasse2', 0, 0),
(3, 'Sophie Lefevre', 'sophie@example.com', 'motdepasse3', 0, 0),
(19, 'Simoniaque', 'simon.auriac@limayrac.fr', 'azerty', 0, 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `adresses_utilisateurs`
--
ALTER TABLE `adresses_utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

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
-- Index pour la table `details_paniers`
--
ALTER TABLE `details_paniers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `panier_id` (`panier_id`),
  ADD KEY `produit_id` (`produit_id`);

--
-- Index pour la table `materiaux`
--
ALTER TABLE `materiaux`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `messages_contact`
--
ALTER TABLE `messages_contact`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `moyens_paiement`
--
ALTER TABLE `moyens_paiement`
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
-- Index pour la table `produits_materiaux`
--
ALTER TABLE `produits_materiaux`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_produit_materiau` (`produit_id`,`materiau_id`),
  ADD KEY `produits_materiaux_materiau_fk` (`materiau_id`);

--
-- Index pour la table `tokens_reinitialisation_mdp`
--
ALTER TABLE `tokens_reinitialisation_mdp`
  ADD PRIMARY KEY (`utilisateur_id`);

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
-- AUTO_INCREMENT pour la table `adresses_utilisateurs`
--
ALTER TABLE `adresses_utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `categories_en_avant`
--
ALTER TABLE `categories_en_avant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=187;

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `details_commandes`
--
ALTER TABLE `details_commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pour la table `details_paniers`
--
ALTER TABLE `details_paniers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT pour la table `materiaux`
--
ALTER TABLE `materiaux`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `messages_contact`
--
ALTER TABLE `messages_contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `moyens_paiement`
--
ALTER TABLE `moyens_paiement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `paniers`
--
ALTER TABLE `paniers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT pour la table `produits_en_avant`
--
ALTER TABLE `produits_en_avant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT pour la table `produits_materiaux`
--
ALTER TABLE `produits_materiaux`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=209;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `adresses_utilisateurs`
--
ALTER TABLE `adresses_utilisateurs`
  ADD CONSTRAINT `adresses_utilisateurs_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

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
-- Contraintes pour la table `details_paniers`
--
ALTER TABLE `details_paniers`
  ADD CONSTRAINT `details_paniers_ibfk_1` FOREIGN KEY (`panier_id`) REFERENCES `paniers` (`id`),
  ADD CONSTRAINT `details_paniers_ibfk_2` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`);

--
-- Contraintes pour la table `moyens_paiement`
--
ALTER TABLE `moyens_paiement`
  ADD CONSTRAINT `moyens_paiement_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`);

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
-- Contraintes pour la table `produits_materiaux`
--
ALTER TABLE `produits_materiaux`
  ADD CONSTRAINT `produits_materiaux_materiau_fk` FOREIGN KEY (`materiau_id`) REFERENCES `materiaux` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `produits_materiaux_produit_fk` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tokens_reinitialisation_mdp`
--
ALTER TABLE `tokens_reinitialisation_mdp`
  ADD CONSTRAINT `tokens_reinitialisation_mdp_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tokens_verification_mail`
--
ALTER TABLE `tokens_verification_mail`
  ADD CONSTRAINT `tokens_verification_mail_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

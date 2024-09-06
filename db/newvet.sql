-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 06 sep. 2024 à 20:47
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
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `est_actif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `adresses_utilisateurs`
--

INSERT INTO `adresses_utilisateurs` (`id`, `utilisateur_id`, `adresse_complète`, `ville`, `code_postal`, `pays`, `date_creation`, `est_actif`) VALUES
(0, 1, 'test', 'test', 'test', 'test', '2024-08-28 23:39:06', 1),
(7, 1, 'test2', 'azdlazkjgdia', 'zadazmouihdj', 'azdoazuihid', '2024-08-28 23:57:43', 1),
(9, 1, 'Avenue de la paix', 'Toulouse', '31400', 'France', '2024-08-29 00:47:05', 1),
(10, 1, 'Rue des champs Elysées ', 'Paris', '75000', 'France', '2024-08-29 00:47:28', 1);

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `est_actif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `nom`, `description`, `est_actif`) VALUES
(1, 'Robes', 'Découvrez notre collection de robes qui combine élégance, confort et tendance. Que vous cherchiez une robe fluide pour l\'été, une robe de soirée chic ou une robe décontractée pour le quotidien, nous avons le modèle parfait pour chaque occasion', 1),
(2, 'Hauts', 'Explorez notre sélection de hauts, conçus pour s\'adapter à toutes vos envies et à chaque moment de la journée. Du t-shirt basique au chemisier raffiné, en passant par les blouses et les débardeurs, chaque pièce est pensée pour offrir confort et élégance. ', 1),
(3, 'Pantalons', 'Nos pantalons allient style et fonctionnalité pour vous offrir une allure impeccable. Que vous préfériez les coupes ajustées, les pantalons larges ou les modèles plus casual, notre collection répondra à toutes vos attentes. Fabriqués avec des matériaux de', 1),
(4, 'Chaussures', 'Complétez votre tenue avec notre gamme de chaussures, alliant confort et style pour chaque pas. Des baskets tendances aux escarpins élégants, en passant par les sandales d\'été et les bottines hivernales, trouvez la paire qui correspond à votre style et à ', 1),
(5, 'Accessoires', 'Les accessoires sont la touche finale qui sublime votre look. Explorez notre collection de sacs, bijoux, ceintures, et autres petits trésors pour personnaliser vos tenues avec style. Que vous cherchiez une pièce discrète ou un accessoire statement, notre ', 1),
(9, 'Pulls', 'Découvrez notre collection de pulls, conçus pour vous offrir chaleur et style. Des classiques intemporels aux modèles tendance, nos pulls ajoutent une touche de confort à vos tenues tout au long de l’année. Trouvez votre pièce idéale pour allier douceur e', 1),
(10, 'Vestes', 'Alliant élégance et praticité, nos modèles vous assurent un look impeccable et un confort optimal. Trouvez la veste parfaite pour compléter votre tenue et affronter chaque journée avec style.', 0);

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
  `statut` int(1) NOT NULL DEFAULT 1,
  `adresse_de_facturation` int(11) NOT NULL,
  `adresse_de_livraison` int(11) NOT NULL,
  `moyen_de_paiement` int(11) NOT NULL,
  `est_actif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id`, `utilisateur_id`, `date_creation`, `prix_total`, `statut`, `adresse_de_facturation`, `adresse_de_livraison`, `moyen_de_paiement`, `est_actif`) VALUES
(1, 1, '2024-06-21', 119.98, 4, 0, 0, 0, 1),
(2, 2, '2024-06-21', 89.97, 2, 0, 0, 0, 1),
(3, 1, '2024-08-22', 219.96, 3, 0, 0, 0, 1),
(4, 2, '2024-08-23', 299.97, 2, 0, 0, 0, 1),
(5, 3, '2024-08-24', 79.98, 4, 0, 0, 0, 1),
(6, 1, '2024-08-25', 159.98, 1, 0, 0, 0, 1),
(7, 2, '2024-08-26', 49.99, 4, 0, 0, 0, 1),
(8, 3, '2024-08-27', 249.95, 3, 0, 0, 0, 1),
(9, 1, '2024-08-28', 399.95, 2, 0, 0, 0, 1),
(12, 1, '2024-08-29', 79.99, 1, 7, 7, 0, 1),
(13, 1, '2024-08-29', 79.99, 1, 0, 0, 0, 1),
(14, 1, '2024-08-29', 79.99, 1, 0, 7, 0, 1),
(15, 1, '2024-08-29', 79.99, 1, 9, 10, 5, 1),
(16, 1, '2024-08-29', 40.00, 1, 9, 10, 5, 1),
(17, 1, '2024-08-29', 109.98, 1, 0, 0, 0, 1),
(18, 1, '2024-08-29', 49.00, 1, 0, 0, 0, 1),
(30, 1, '2024-08-29', 29.99, 1, 10, 10, 5, 1),
(31, 1, '2024-08-29', 14.99, 1, 10, 10, 5, 1);

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
(21, 9, 7, 1),
(28, 12, 2, 1),
(29, 13, 2, 1),
(30, 14, 2, 1),
(31, 15, 2, 1),
(32, 16, 1, 1),
(33, 17, 2, 1),
(34, 17, 3, 1),
(35, 18, 10, 1),
(44, 30, 3, 1),
(45, 31, 4, 1);

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

-- --------------------------------------------------------

--
-- Structure de la table `materiaux`
--

CREATE TABLE `materiaux` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `est_actif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `materiaux`
--

INSERT INTO `materiaux` (`id`, `nom`, `est_actif`) VALUES
(1, 'Coton', 1),
(2, 'Polyester', 1),
(3, 'Cuir', 1),
(4, 'Laine', 1),
(5, 'Soie', 1);

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
  `date_ajout` timestamp NOT NULL DEFAULT current_timestamp(),
  `est_actif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `moyens_paiement`
--

INSERT INTO `moyens_paiement` (`id`, `utilisateur_id`, `type`, `numero_carte`, `nom_titulaire`, `date_expiration`, `cvv`, `paypal_email`, `date_ajout`, `est_actif`) VALUES
(0, 19, 'paypal', '', '', '0000-00-00', '', 'simon.auriac@limayrac.fr', '2024-08-25 18:25:07', 1),
(2, 19, 'card', '00', 'auriac simon', '0000-00-00', '000', '', '2024-08-25 18:26:06', 1),
(4, 1, 'card', 'azdazkbdim', 'zjahdmoiazjd', '0000-00-00', '477', '', '2024-08-29 00:05:59', 1),
(5, 1, 'card', '1234 5678 9123 4567', 'Emma Martin', '0000-00-00', '200', '', '2024-08-29 00:48:06', 1);

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
  `en_priorite` tinyint(1) NOT NULL DEFAULT 0,
  `est_actif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id`, `categorie_id`, `nom`, `description`, `prix`, `stock`, `date_ajout`, `en_priorite`, `est_actif`) VALUES
(1, 1, 'Robe Zébrée', 'Robe zébrée élégante pour toutes les occasion', 40.00, 30, '2024-08-13', 0, 1),
(2, 1, 'Robe Maxi Florale', 'Robe longue avec motif floral, parfaite pour l&#039;été.', 79.99, 30, '2024-08-09', 0, 1),
(3, 2, 'Chemisier à Manches Longues', 'Chemisier féminin à manches longues.', 29.99, 80, '2024-04-24', 0, 1),
(4, 2, 'Débardeur Basique', 'Débardeur confortable pour une tenue décontractée.', 14.99, 100, '2024-02-16', 0, 1),
(5, 3, 'Jean Skinny Stretch', 'Jean stretch et ajusté pour un look moderne.', 49.99, 0, '2024-08-21', 0, 0),
(6, 4, 'Bottes Hautes', 'Bottes élégantes pour l&#039;hiver.', 89.99, 0, '2024-08-28', 1, 1),
(7, 4, 'Sandales à Talons', 'Sandales à talons pour les soirées.', 69.99, 20, '2024-01-10', 1, 1),
(10, 4, 'Chaussure à talon', 'Chaussure à talon en cuir', 49.00, 20, '2018-08-15', 0, 1),
(11, 3, 'Pantalon Onlraffy-Yo Life', 'Ce produit est fabriqué à partir de polyester recyclé. Le polyester recyclé préserve les ressources naturelles et réduit la quantité de déchets.', 39.99, 50, '2024-08-20', 0, 1),
(13, 5, 'Sac paille', 'Sac rond en paille avec anses dorées', 25.00, 50, '2024-08-26', 0, 1),
(22, 3, 'Pantallon Habillé', 'Pantalon fluide à jambes larges, taille réglable avec liens ton sur ton et poches sur les côtés', 35.00, 50, '2024-08-26', 0, 1),
(28, 5, ' Lunette de Soleil Polarisées', 'Protection UV400 Surdimensionnées Lunettes Classique Grand Cadre Lunettes de Soleil Femmes B2289', 19.99, 100, '2024-08-26', 0, 1),
(32, 1, 'Pull noir en coton', 'Pull noir confortable et chaud', 35.00, 50, '2024-08-28', 0, 1);

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

--
-- Déchargement des données de la table `tokens_verification_mail`
--

INSERT INTO `tokens_verification_mail` (`utilisateur_id`, `token`, `date_max`) VALUES
(50, '8134ef29f7fb4636e3ca1e246608bfdb81fd087afb026b3213b28bb0b2d5ad37', '2024-09-06 20:59:58');

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
  `mail_verifie` tinyint(1) DEFAULT 0,
  `est_actif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `email`, `mot_de_passe`, `est_admin`, `mail_verifie`, `est_actif`) VALUES
(1, 'Emma martin', 'emma@example.com', '$2y$10$6ElWRb3K2iqxKEMbdJItaOzlsEW25XyVAMBNWXaYIvNbRkQg70j9C', 0, 1, 1),
(2, 'Laura Dubois', 'laura@example.com', 'motdepasse2', 0, 0, 1),
(3, 'Sophie Lefevre', 'sophie@example.com', 'motdepasse3', 0, 0, 1),
(19, 'Simoniaque', 'simon.auriac@limayrac.fr', '$2y$10$RzVdvHwrXJu9Zqz4aWTqOeqD7bpEjyJSWA.kzGti3obYTLO11bpPm', 0, 1, 0),
(50, 'Test', 'mateus.Fariasfreire@limayrac.fr', '$2y$10$Tte9rZ6vx6E2BNAeH..Gv.uLIRlv1ehpsyvGDUGMi6lVlSCLE9/Iq', 0, 0, 1);

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
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `fk_commandes_adresse_facturation` (`adresse_de_facturation`),
  ADD KEY `fk_commandes_adresse_livraison` (`adresse_de_livraison`),
  ADD KEY `fk_commandes_moyen_paiement` (`moyen_de_paiement`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT pour la table `details_commandes`
--
ALTER TABLE `details_commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT pour la table `details_paniers`
--
ALTER TABLE `details_paniers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

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
  ADD CONSTRAINT `commandes_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_commandes_adresse_facturation` FOREIGN KEY (`adresse_de_facturation`) REFERENCES `adresses_utilisateurs` (`id`),
  ADD CONSTRAINT `fk_commandes_adresse_livraison` FOREIGN KEY (`adresse_de_livraison`) REFERENCES `adresses_utilisateurs` (`id`),
  ADD CONSTRAINT `fk_commandes_moyen_paiement` FOREIGN KEY (`moyen_de_paiement`) REFERENCES `moyens_paiement` (`id`);

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

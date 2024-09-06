<?php 
function SearchProducts($pdo, $searchText, $minPrice, $maxPrice, $inStock) {
    
    // Récupère tous les produits
    $products = GetProducts($pdo);
    $searchText = formaterString($searchText);

    // Si le texte de recherche est vide, filtre les produits selon les prix et stock
    if ($searchText == "") {
        $filteredProducts = array_filter($products, function($product) use ($minPrice, $maxPrice, $inStock) {
            return (!$inStock || $product['stock'] > 0) && $product['prix'] >= $minPrice && $product['prix'] <= $maxPrice;
        });

        // Met les produits hors stock à la fin
        usort($filteredProducts, function($a, $b) {
            return $a['stock'] <= 0 ? 1 : ($b['stock'] <= 0 ? -1 : 0);
        });

        return $filteredProducts;
    }

    // Initialiser un tableau pour les produits à afficher
    $productsToDisplay = array();

    foreach ($products as $product) {
        $productID = $product['id'];
        $productName = formaterString($product['nom']);
        $productPrice = $product['prix'];
        $productStock = $product['stock'];
        $productDescription = formaterString($product['description']);
        $productCategoryName = formaterString(GetCategoryById($pdo, $product['categorie_id'])['nom']);
        $productMaterials = GetProductMaterials($pdo, $productID);

        // Filtrage des produits par prix et stock
        if (($inStock && $productStock <= 0) || $productPrice > $maxPrice || $productPrice < $minPrice) {
            continue;
        }

        $priority = null;

        if($productStock <= 0){
            $priority = 6;
        }

        // Priorité 1 : Correspondance exacte avec le nom du produit
        if ($productName == $searchText) {
            $priority == 6 ? $priority = 6 : $priority = 1; 
            continue;
        }

        // Priorité 2 : Un caractère de différent avec le nom du produit
        if (abs(strlen($productName) - strlen($searchText)) == 1) {
            if (isOneCharDifference($productName, $searchText)) {
                $priority == 6 ? $priority = 6 : $priority = 2; 
                addProductToDisplay($productsToDisplay, $product, $priority);
                continue;
            }
        }

        // Priorité 3 : Débute avec le nom du produit
        if (strpos($productName, $searchText) === 0) {
            
            $priority == 6 ? $priority = 6 : $priority = 3; 
            addProductToDisplay($productsToDisplay, $product, $priority);
            continue;
        }

        // Priorité 4 : Contient le nom du produit
        if (strpos($productName, $searchText) !== false) {
            $priority == 6 ? $priority = 6 : $priority = 4; 
            addProductToDisplay($productsToDisplay, $product, $priority);
            continue;
        }

        // Priorité 5 : Produits trouvés via la catégorie, les matériaux ou la description
        if (strpos($productDescription, $searchText) !== false || strpos($productCategoryName, $searchText) !== false) {
            $priority == 6 ? $priority = 6 : $priority = 5; 
            addProductToDisplay($productsToDisplay, $product, $priority);
            continue;
        }
        
        foreach ($productMaterials as $material) {
            if (formaterString($material['nom']) == $searchText) {
                $priority == 6 ? $priority = 6 : $priority = 5;
                addProductToDisplay($productsToDisplay, $product, $priority);
                continue 2;
            }
        }
    }

    //log each product priority
    foreach($productsToDisplay as $product){
        debugToConsole($product['product']['nom'] . " : " . $product['priority']);
    }

    // Trier les produits selon la priorité
    usort($productsToDisplay, function($a, $b) {
        return $a['priority'] <=> $b['priority'];
    });

    // Retourner seulement les produits triés
    return array_map(function($item) {
        return $item['product'];
    }, $productsToDisplay);
}

function addProductToDisplay(&$productsToDisplay, $product, $priority){
    $productsToDisplay[] = ['product' => $product, 'priority' => $priority];
}

// Fonction utilitaire pour vérifier une différence d'un seul caractère
function isOneCharDifference($string1, $string2) {
    $len1 = strlen($string1);
    $len2 = strlen($string2);
    $minLen = min($len1, $len2);

    for ($i = 0; $i < $minLen; $i++) {
        if ($string1[$i] !== $string2[$i]) {
            // Teste en enlevant ou en ajoutant un caractère
            return substr_replace($string1, "", $i, 1) === $string2 || substr_replace($string2, "", $i, 1) === $string1;
        }
    }

    return abs($len1 - $len2) === 1;
}

function formaterString($str) {
    // Tableau des caractères accentués et leurs équivalents non accentués
    $accents = [
        'à', 'â', 'ä', 'á', 'ã', 'å', 'À', 'Â', 'Ä', 'Á', 'Ã', 'Å',
        'è', 'ê', 'ë', 'é', 'È', 'Ê', 'Ë', 'É',
        'ì', 'î', 'ï', 'í', 'Ì', 'Î', 'Ï', 'Í',
        'ò', 'ô', 'ö', 'ó', 'õ', 'Ò', 'Ô', 'Ö', 'Ó', 'Õ',
        'ù', 'û', 'ü', 'ú', 'Ù', 'Û', 'Ü', 'Ú',
        'ç', 'Ç',
        'ñ', 'Ñ'
    ];

    $sans_accents = [
        'a', 'a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A', 'A', 'A',
        'e', 'e', 'e', 'e', 'E', 'E', 'E', 'E',
        'i', 'i', 'i', 'i', 'I', 'I', 'I', 'I',
        'o', 'o', 'o', 'o', 'o', 'O', 'O', 'O', 'O', 'O',
        'u', 'u', 'u', 'u', 'U', 'U', 'U', 'U',
        'c', 'C',
        'n', 'N'
    ];

    // Remplacer les caractères accentués par leurs équivalents sans accents
    $str = str_replace($accents, $sans_accents, $str);

    // Transformer la chaîne en minuscules
    $str = strtolower($str);

    // Supprimer les doubles espaces
    $str = preg_replace('/\s+/', ' ', $str);

    // Supprimer les espaces au début et à la fin
    $str = trim($str);

    return $str;
}

?>
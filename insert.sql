
/* user  */
INSERT INTO `user` (`id`, `email`, `username`, `roles`, `password`) VALUES
(1, 'monEmail1', 'test1', '["ROLE_USER"]', 'mdp'),
(2, 'monEmail2', 'test3', '["ROLE_ADMIN"]', 'mdp');


INSERT INTO `category` (`id`, `name`) VALUES
(1, 'Legumes'),
(2, 'Fruits'),
(3, 'Boisson');

INSERT INTO `contact` (`id`, `name`, `email`, `description`) VALUES
(1, 'lorem ipsum', 'monEmail1', 'lorem ipsum lorem ipsum lorem ipsum lorem ipsum lorem ipsum');


INSERT INTO `newsletter` (`id`, `email`) VALUES
(1, 'monEmail1 ');


INSERT INTO `tva` (`id`, `multiplicate`, `nom`, `valeur`) VALUES
(1, 0.2, 'taux normal', 20),
(2, 0.1, 'taux intermédiaire', 10),
(3, 0.055, 'taux réduit', 5.5);


INSERT INTO `utilisateur_adresse` (`id`, `user_id`, `nom`, `prenom`, `telephone`, `adresse`, `cp`, `pays`, `ville`, `complement`) VALUES
(1, 1, 'monNameUser', 'monPrenomUser', 'lorem ipsum', 'lorem ipsum', 'lorem ipsum', 'lorem ipsum', 'lorem ipsum', 'lorem ipsum');

INSERT INTO `product` (`id`, `tva_id`, `name`, `price`, `description`, `content`, `file_name`, `promo`, `stock`) VALUES
(1, 2, 'ARTICHAUT', 15, 'lorem ipsum ', 'lorem ipsum ', 'ARTICHAUT.jpg', 1, 18);

INSERT INTO `product_category` (`product_id`, `category_id`) VALUES
(1, 1),
(1, 2),
(1, 3);


-- Dados de desenvolvimento (permissões / auth)
-- Senha de todos os usuários: Senha12345

USE api_tattoo_php_lab;

INSERT INTO users (id, email, password_hash, name, role) VALUES
    (1, 'ana.artist@lab.test', '$2y$12$Ofe6SXdpaviUsRUAoWdO/.SbJUmev/dlswZSStdz.a66wSj6xoXlO', 'Ana Tatuadora', 'artist'),
    (2, 'bruno.artist@lab.test', '$2y$12$Ofe6SXdpaviUsRUAoWdO/.SbJUmev/dlswZSStdz.a66wSj6xoXlO', 'Bruno Ink', 'artist'),
    (3, 'carla.client@lab.test', '$2y$12$Ofe6SXdpaviUsRUAoWdO/.SbJUmev/dlswZSStdz.a66wSj6xoXlO', 'Carla Cliente', 'client'),
    (4, 'diego.client@lab.test', '$2y$12$Ofe6SXdpaviUsRUAoWdO/.SbJUmev/dlswZSStdz.a66wSj6xoXlO', 'Diego Cliente', 'client');

INSERT INTO artists (id, studio_name, city, state, user_id) VALUES
    (1, 'Ink Balneário', 'Balneário Camboriú', 'SC', 1),
    (2, 'Tinta Itajaí', 'Itajaí', 'SC', 2),
    (3, 'Estúdio Órfão', 'Florianópolis', 'SC', NULL);

INSERT INTO reviews (artist_id, rating, comment) VALUES
    (1, 5, 'Linha fina impecável.'),
    (1, 4, 'Ambiente limpo, recomendo.'),
    (2, 3, 'Demorou mas ficou bom.'),
    (3, 5, 'Review no estúdio ainda sem dono vinculado.');

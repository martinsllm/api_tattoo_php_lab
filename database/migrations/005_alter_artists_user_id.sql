USE api_tattoo_php_lab;

ALTER TABLE artists
    ADD COLUMN user_id INT NULL,
    ADD CONSTRAINT fk_artists_user_id
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL;

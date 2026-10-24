USE api_tattoo_php_lab;

ALTER TABLE artist_images
    ADD COLUMN stored_path VARCHAR(512) NOT NULL DEFAULT '' AFTER image_url;

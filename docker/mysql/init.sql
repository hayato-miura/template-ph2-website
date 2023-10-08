-- Create the 'studies' table
CREATE TABLE studies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    study_at DATETIME NOT NULL,
    study_hours DECIMAL(5, 2) NOT NULL DEFAULT 0
);

-- Create the 'contents' table
CREATE TABLE contents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

-- Create the 'languages' table
CREATE TABLE languages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

-- Create the 'study_contents' junction table
CREATE TABLE study_contents (
    study_id INT,
    content_id INT,
    FOREIGN KEY (study_id) REFERENCES studies(id),
    FOREIGN KEY (content_id) REFERENCES contents(id)
);

-- Create the 'study_languages' junction table
CREATE TABLE study_languages (
    study_id INT,
    language_id INT,
    FOREIGN KEY (study_id) REFERENCES studies(id),
    FOREIGN KEY (language_id) REFERENCES languages(id)
);


INSERT INTO contents (name) VALUES
    ('ドットインストール'),
    ('N予備校'),
    ('POSSE課題');

    INSERT INTO languages (name) VALUES
    ('HTML'),
    ('CSS'),
    ('JavaScript'),;


-- Insert dummy data into 'studies' table
INSERT INTO studies (study_at, study_hours) VALUES
    ('2023-10-01 08:00:00', 2),
    ('2023-10-02 09:30:00', 3),
    ('2023-10-03 07:45:00', 2),
    ('2023-10-04 10:15:00', 4),
    ('2023-10-05 06:30:00', 2),
    ('2023-10-06 08:45:00', 3),
    ('2023-10-07 07:15:00', 5);

-- Insert dummy data into 'study_contents' table
INSERT INTO study_contents (study_id, content_id) VALUES
    (1, 1),  -- Content 1 for Study 1
    (2, 2),  -- Content 2 for Study 2
    (3, 1),  -- Content 1 for Study 3
    (4, 3),  -- Content 3 for Study 4
    (5, 2),  -- Content 2 for Study 5
    (6, 1),  -- Content 1 for Study 6
    (7, 2);  -- Content 2 for Study 7

-- Insert dummy data into 'study_languages' table
INSERT INTO study_languages (study_id, language_id) VALUES
    (1, 1),  -- Language 1 for Study 1
    (2, 2),  -- Language 2 for Study 2
    (3, 1),  -- Language 1 for Study 3
    (4, 3),  -- Language 3 for Study 4
    (5, 2),  -- Language 2 for Study 5
    (6, 1),  -- Language 1 for Study 6
    (7, 2);  -- Language 2 for Study 7;

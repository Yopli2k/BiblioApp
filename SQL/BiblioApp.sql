/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

 /**
 * Script for database creation.
 *
 * @author José Antonio Cuello Principal <yopli2000@gmail.com>
 * @version 1.0
 */

/* create database */
CREATE DATABASE biblioapp
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_spanish_ci
    DEFAULT ENCRYPTION='N';

/**
 * Create users table.
 * The users are the people who can access to the application
 * for manage the users, members, books and other data.
 */
CREATE TABLE users (
    email varchar(100) NOT NULL,
    enabled bool NOT NULL DEFAULT true,
    logkey varchar(100),
    name varchar(100) NOT NULL,
    username varchar(50) NOT NULL,
    password varchar(255) NOT NULL,
    CONSTRAINT users_pk PRIMARY KEY (username)
);

CREATE UNIQUE INDEX users_idx_email ON users (email);

INSERT INTO users (email, name, username, password) VALUES ('admin@biblioapp.com', 'Administrador', 'admin', '$2y$10$/dO7uCXqeqVe32O1GRDFT.f6tN3VCSaKvF9oFwLfOsxzn9NF7HDJy');


/**
 * Create members table.
 * The members are the people who can borrow books.
 */
CREATE TABLE members (
     address varchar(100),
     creationdate date NOT NULL,
     document varchar(30) NOT NULL,
     email varchar(100) NOT NULL,
     enabled bool NOT NULL DEFAULT true,
     id int NOT NULL AUTO_INCREMENT,
     logkey varchar(100),
     name varchar(100) NOT NULL,
     notes text,
     password varchar(255),
     phone varchar(10) NOT NULL,
     verified bool DEFAULT false,
     CONSTRAINT members_pk PRIMARY KEY (id)
);

CREATE UNIQUE INDEX members_idx_email ON members (email);
CREATE INDEX members_idx_name ON members (name);

INSERT INTO members (name,phone,document,email,creationdate,notes,address,password,verified,enabled) VALUES
    ('Juan Pérez','1234567890','A12345678','juanperez@email.com','2023-01-15',NULL,'Calle 123, Ciudad',NULL,1,1),
    ('María López','9876543210','X9876543Z','marialopez@email.com','2023-02-20',NULL,'Avenida 456, Pueblo',NULL,0,1),
    ('Carlos Rodríguez','5555555555','Y8765432A','carlos@email.com','2023-03-10',NULL,'Plaza 789, Villa',NULL,1,0),
    ('Ana Martínez','5551234567','B87654321','anamartinez@email.com','2023-04-05','Esto son unas pruebas de notas','Carrera 12, Aldea','',1,1),
    ('Pedro Sánchez','3339998888','C7654321B','pedrosanchez@email.com','2023-05-20',NULL,'Camino 45, Pueblo Nuevo',NULL,0,1),
    ('Laura González','1112223333','D6543210C','lauragonzalez@email.com','2023-06-30',NULL,'Calle 67, Ciudad Vieja',NULL,1,0),
    ('Manuel García','4445556666','E543210CD','manuelgarcia@email.com','2023-07-10',NULL,'Avenida 89, Villa Nueva',NULL,1,1),
    ('Isabel Fernández','6667778888','F43210CDE','isabelfernandez@email.com','2023-08-15',NULL,'Paseo 23, Pueblo Alto',NULL,0,1),
    ('Miguel Pérez','2223334444','G3210CDEF','miguelperez@email.com','2023-09-10','','Ruta 56, Aldea Antigua','',1,1),
    ('Carmen López','8889990000','H210CDEFG','carmenlopez@email.com','2023-10-05','','Calle 1, Ciudad Nueva','',1,1);
INSERT INTO members (name,phone,document,email,creationdate,notes,address,password,verified,enabled) VALUES
    ('Javier Rodríguez','7778889999','I10CDEFGH','javierrodriguez@email.com','2023-11-20',NULL,'Carrera 2, Villa Vieja',NULL,1,1),
    ('Elena Martínez','3334445555','J0CDEFGHI','elenamartinez@email.com','2023-12-30','','Camino 3, Pueblo Antiguo','',1,1),
    ('Ricardo Sánchez','5554443333','K0CDEFGHIJ','ricardosanchez@email.com','2024-01-10',NULL,'Plaza 7, Aldea Nueva',NULL,1,0),
    ('Sara González','1112223334','L0CDEFGHIJK','saragonzalez@email.com','2024-02-15','','Avenida 8, Ciudad Antigua','',1,1),
    ('Pablo García','4445556665','M0CDEFGHIJKL','pablogarcia@email.com','2024-03-10',NULL,'Calle 9, Pueblo Moderno',NULL,1,0),
    ('Luis Fernández','6667778889','N0CDEFGHIJKLM','luisfernandez@email.com','2024-04-05','','Carrera 10, Villa Moderna','',1,1),
    ('Eva Pérez','2223334446','O0CDEFGHIJKLMN','evaperez@email.com','2024-05-20','','Paseo 11, Aldea Moderna','',1,1),
    ('Carlos López','8889990001','P0CDEFGHIJKLMNO','carloslopez@email.com','2024-06-30',NULL,'Ruta 12, Ciudad Moderna',NULL,0,1),
    ('Ana Rodríguez','7778889992','Q0CDEFGHIJKLMNOP','anarodriguez@email.com','2024-07-10','','Camino 13, Pueblo Clásico','',1,1);


/**
 * Create categories table.
 * The categories are the groups of books depending of his theme.
 */
CREATE TABLE categories (
    id int NOT NULL AUTO_INCREMENT,
    name varchar(100) NOT NULL,
    CONSTRAINT categories_pk PRIMARY KEY (id)
);

INSERT INTO categories (name) VALUES ('Devocionales');
INSERT INTO categories (name) VALUES ('Escuela Dominical (3-18 años)');
INSERT INTO categories (name) VALUES ('Niños');
INSERT INTO categories (name) VALUES ('Estudios Bíblicos');
INSERT INTO categories (name) VALUES ('Familia');
INSERT INTO categories (name) VALUES ('Teología');
INSERT INTO categories (name) VALUES ('Comentarios bíblicos');
INSERT INTO categories (name) VALUES ('Mujer');
INSERT INTO categories (name) VALUES ('Vida Cristiana');
INSERT INTO categories (name) VALUES ('Jóvenes/Adolescentes');
INSERT INTO categories (name) VALUES ('Ayudas para el ministerio y Liderazgo');
INSERT INTO categories (name) VALUES ('Evangelismo');
INSERT INTO categories (name) VALUES ('Apologética');
INSERT INTO categories (name) VALUES ('Guerra Espiritual');
INSERT INTO categories (name) VALUES ('Finanzas');
INSERT INTO categories (name) VALUES ('Referencia');
INSERT INTO categories (name) VALUES ('Hombres');
INSERT INTO categories (name) VALUES ('Testimonios y biografias');
INSERT INTO categories (name) VALUES ('Oración/Adoración');
INSERT INTO categories (name) VALUES ('Profecía');
INSERT INTO categories (name) VALUES ('Estudios para grupo');
INSERT INTO categories (name) VALUES ('Misiones');
INSERT INTO categories (name) VALUES ('Salud');
INSERT INTO categories (name) VALUES ('Actualidad');
INSERT INTO categories (name) VALUES ('Historia');
INSERT INTO categories (name) VALUES ('Novelas');
INSERT INTO categories (name) VALUES ('Poesía');
INSERT INTO categories (name) VALUES ('Psicología');
INSERT INTO categories (name) VALUES ('Teatro');
INSERT INTO categories (name) VALUES ('Tratados');

/**
 * Create books table.
 * The books are the main data of the application.
 */
CREATE TABLE books (
    author varchar(100) NOT NULL,
    editorial varchar(100) NOT NULL,
    id int NOT NULL AUTO_INCREMENT,
    isbn varchar(13) NOT NULL,
    name varchar(100) NOT NULL,
    pages int NOT NULL DEFAULT 0,
    publication int NOT NULL,
    recommended bool NOT NULL DEFAULT false,
    synopsis text NOT NULL,
    CONSTRAINT books_pk PRIMARY KEY (id)
);

CREATE UNIQUE INDEX books_idx_isbn ON books (isbn);
CREATE INDEX books_idx_name ON books (name);

INSERT INTO books (author,isbn,synopsis,name,editorial,pages,publication) VALUES
    ('Ana Rodríguez','9781234567890','Un devocional diario para fortalecer la fe y la relación con Dios.','Renovación Diaria','Salva Ediciones',365,2022),
    ('Juan Pérez','9782345678901','Material de enseñanza para escuela dominical enfocado en jóvenes de 15 a 18 años.','Juventud en la Biblia','Ediciones El Rey',160,2021),
    ('Luisa Martínez','9783456789012','Un libro ilustrado para niños que presenta historias bíblicas de manera atractiva.','Biblia para Niños','Editorial Infantil',40,2020),
    ('Carlos García','9784567890123','Un estudio profundo de los libros proféticos del Antiguo Testamento.','Profecías Antiguas','Salva Ediciones',280,2019),
    ('Sofía López','9785678901234','Consejos y guía para construir un matrimonio sólido desde una perspectiva cristiana.','Matrimonio en Cristo','Ediciones Familia',200,2018),
    ('María Rodríguez','9786789012345','Una exploración teológica de la Trinidad y la naturaleza de Dios.','La Trinidad Divina','Salva Ediciones',220,2017),
    ('David Soto','9787890123456','Un comentario detallado sobre el Evangelio de Juan.','Comentario sobre el Evangelio de Juan','Salva Ediciones',350,2016),
    ('Laura Pérez','9788901234567','Un libro de inspiración para mujeres que buscan crecer en su fe.','Mujeres en la Fe','Nuestro Pastor',180,2015),
    ('Javier Martínez','9789012345678','Una guía práctica para vivir una vida cristiana auténtica en la actualidad.','Viviendo en Cristo','Ediciones El Rey',240,2014),
    ('Isabella Sánchez','9781234567891','Un devocional para jóvenes y adolescentes que aborda temas relevantes.','Fe en la Adolescencia','Nuestro Pastor',365,2023);
INSERT INTO books (author,isbn,synopsis,name,editorial,pages,publication) VALUES
    ('Antonio López','9782345678902','Un libro de liderazgo centrado en principios cristianos para líderes de iglesia.','Liderazgo Espiritual','Editorial Ministerial',280,2021),
    ('Elena Pérez','9783456789013','Una guía paso a paso para el evangelismo efectivo y cómo compartir la fe.','Evangelismo Práctico','Ediciones El Rey',200,2020),
    ('Gabriel Martínez','9784567890124','Un libro que aborda preguntas frecuentes sobre la fe cristiana.','Apologetica en Preguntas','Nuestro Pastor',250,2019),
    ('María López','9785678901235','Una guía para la guerra espiritual y cómo resistir al enemigo.','Venciendo en la Batalla Espiritual','Nuestro Pastor',220,2018),
    ('Carlos González','9786789012346','Consejos financieros basados en principios bíblicos para la gestión de dinero.','Finanzas en Abundancia','Magallanes',180,2017),
    ('Sofía Martínez','9787890123457','Un libro de referencia con versículos bíblicos y concordancia.','Biblia de Referencia','Nuestro Pastor',400,2016),
    ('Juan Rodríguez','9788901234568','Un libro de inspiración para hombres que buscan crecer en su fe y liderazgo.','Hombres de Valor','Ediciones El Rey',200,2015),
    ('Luisa López','9789012345679','Historias de testimonios y biografías de líderes cristianos.','Testimonios de Fe','Nuestro Pastor',280,2014),
    ('David Martínez','9780123456780','Un libro sobre la importancia de la oración y la adoración en la vida cristiana.','Oración y Adoración','Salva Ediciones',220,2013),
    ('Laura Soto','9782345678903','Un análisis de la profecía bíblica y su relevancia en la actualidad.','Profecía en el Siglo XXI','Magallanes',250,2012);
INSERT INTO books (author,isbn,synopsis,name,editorial,pages,publication) VALUES
    ('Javier García','9783456789014','Estudios bíblicos diseñados para grupos de discusión en la iglesia.','Estudios en Grupo','Editorial de Estudios',300,2011),
    ('María Rodríguez','9784567890125','Una guía sobre la importancia de las misiones en la expansión del cristianismo.','Misiones Globales','Ediciones El Rey',220,2010),
    ('Carlos Sánchez','9785678901236','Un enfoque cristiano sobre la importancia de la salud y el bienestar.','Vida Saludable','Ediciones Familia',240,2009),
    ('Sofía Pérez','9786789012347','Una exploración de los eventos y desafíos actuales desde una perspectiva cristiana.','Cristianismo en el Siglo XXI','Ediciones El Rey',260,2008),
    ('Antonio Martínez','9787890123458','Un libro que narra la historia del cristianismo desde sus inicios.','Historia del Cristianismo','Salva Ediciones',320,2007),
    ('Elena López','9788901234569','Una novela basada en eventos bíblicos y personajes históricos.','Héroes de la Fe','Magallanes',280,2006),
    ('Gabriel Rodríguez','9789012345670','Una colección de poemas que reflexionan sobre la espiritualidad y la fe.','Poesía Religiosa','Ediciones Familia',150,2005),
    ('María Soto','9780123456781','Una guía sobre psicología desde una perspectiva cristiana.','Psicología y Fe','Ediciones Familia',220,2004),
    ('David Pérez','9782345678904','Un libro de teatro basado en historias y parábolas bíblicas.','Teatro Religioso','Magallanes',180,2003),
    ('Marta Pérez','9781234567892','Un devocional para fortalecer la relación con Dios en momentos de dificultad.','Fe en la Tormenta','Salva Ediciones',365,2022);
INSERT INTO books (author,isbn,synopsis,name,editorial,pages,publication) VALUES
    ('Luis González','9782445678903','Material de enseñanza para la escuela dominical enfocado en niños de 6 a 9 años.','Aventuras en la Biblia','Ediciones El Rey',160,2021),
    ('Isabella Martínez','9784456789013','Un libro de cuentos bíblicos ilustrados para niños pequeños.','Mis Primeras Historias Bíblicas','Editorial Infantil',40,2020),
    ('Javier Soto','9784567890126','Un análisis profundo de la profecía bíblica y su relación con eventos actuales.','Profecía y Actualidad','Salva Ediciones',280,2019),
    ('Elena González','9785678901237','Consejos y reflexiones para fortalecer la familia desde una perspectiva cristiana.','Familia en Cristo','Ediciones Familia',200,2018),
    ('Carlos Martínez','9786789012348','Un libro de teología sistemática que explora las doctrinas cristianas fundamentales.','Teología Fundamental','Salva Ediciones',220,2017),
    ('Laura Sánchez','9787890123459','Un comentario detallado sobre el Evangelio de Mateo.','Comentario sobre el Evangelio de Mateo','Ediciones Familia',350,2016),
    ('David Rodríguez','9788901234560','Un libro de inspiración para mujeres que desean crecer en su relación con Dios.','Mujeres de Fe','Salva Ediciones',180,2015),
    ('Ana López','9789012345671','Un devocional para hombres que buscan crecer en su fe y liderazgo.','Hombres de Fe','Ediciones El Rey',200,2014),
    ('Lucas Pérez','9780123456782','Una guía práctica para la adoración y la vida cristiana en comunidad.','Adoración Comunitaria','Magallanes',240,2013),
    ('Sofía Rodríguez','9782345678905','Un estudio sobre la gracia y el perdón en la vida del creyente.','La Gracia de Dios','Nuestro Pastor',365,2023);
INSERT INTO books (author,isbn,synopsis,name,editorial,pages,publication) VALUES
    ('Juan Soto','9783456789015','Estudios bíblicos diseñados para grupos de discusión en la iglesia local.','Estudios en Comunidad','Ediciones El Rey',160,2022),
    ('Miguel González','9784567890127','Una introducción a la historia de la Iglesia y su influencia en la sociedad.','Historia de la Iglesia','Nuestro Pastor',280,2021),
    ('María López','9785678901238','Un libro de referencia con versículos bíblicos y diccionario teológico.','Diccionario Teológico','Ediciones Familia',400,2020),
    ('Antonio Rodríguez','9786789012349','Una guía para el liderazgo en la iglesia local desde una perspectiva bíblica.','Liderazgo Eclesiástico','Magallanes',280,2019),
    ('Elena Martínez','9787890123460','Un devocional diario para parejas que desean fortalecer su matrimonio.','Matrimonio en Oración','Ediciones Familia',220,2018),
    ('Javier Sánchez','9788901234561','Una guía para jóvenes cristianos sobre cómo enfrentar los desafíos de la vida.','Juventud en Cristo','Nuestro Pastor',240,2017),
    ('Isabella González','9789012345672','Un estudio sobre la influencia de la Biblia en la literatura clásica.','La Biblia en la Literatura','Salva Ediciones',250,2016),
    ('Luisa López','9780123456783','Un análisis de las parábolas de Jesús y sus lecciones para la vida cotidiana.','Lecciones de las Parábolas','Ediciones Familia',220,2015),
    ('David Pérez','9782345678906','Una guía sobre la oración y cómo desarrollar una vida de comunión con Dios.','Oración Intima','Nuestro Pastor',240,2014),
    ('Laura Rodríguez','9783456789016','Un libro sobre la influencia de la música en la adoración cristiana.','Música de Adoración','Sound Majestic',200,2013);
INSERT INTO books (author,isbn,synopsis,name,editorial,pages,publication) VALUES
    ('Carlos Soto','9784567890128','Un análisis de las creencias y prácticas religiosas en la cultura moderna.','Cristianismo en la Posmodernidad','Ediciones El Rey',260,2012);


/**
 * Create books_images table.
 * This table stores the images of the books.
 */
CREATE TABLE books_images (
    book_id int NOT NULL,
    creationdate date NOT NULL,
    creationhour time DEFAULT NULL,
    filename varchar(100) NOT NULL,
    filepath varchar(200) NOT NULL,
    filetype varchar(100) DEFAULT NULL,
    filesize int NOT NULL,
    id int NOT NULL AUTO_INCREMENT,
    CONSTRAINT books_pk PRIMARY KEY (id),
    CONSTRAINT books_images_book_fk FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE ON UPDATE CASCADE
);

/**
 * Create books_categories table.
 * This table is the relation between books and categories.
 */
CREATE TABLE books_categories (
    book_id int NOT NULL,
    category_id int NOT NULL,
    id int NOT NULL AUTO_INCREMENT,
    CONSTRAINT books_categories_pk PRIMARY KEY (id),
    CONSTRAINT books_categories_book_fk FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT books_categories_category_fk FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE UNIQUE INDEX books_categories_idx_1 ON books_categories (book_id, category_id);

/**
 * Create loans table.
 * This table stores book loans made by members.
 */
CREATE TABLE loans (
    book_id int NOT NULL,
    id int NOT NULL AUTO_INCREMENT,
    loan_date date NOT NULL,
    member_id int NOT NULL,
    return_date date NOT NULL,
    CONSTRAINT loans_pk PRIMARY KEY (id),
    CONSTRAINT loans_book_fk FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT loans_member_fk FOREIGN KEY (member_id) REFERENCES members (id) ON DELETE CASCADE ON UPDATE CASCADE
);

/**
 * Create ratings table.
 * This table stores book ratings made by members.
 */
CREATE TABLE ratings (
    book_id int NOT NULL,
    id int NOT NULL AUTO_INCREMENT,
    member_id int NOT NULL,
    rating int NOT NULL,
    rating_date date NOT NULL,
    rating_time time NOT NULL,
    valoration text NOT NULL,
    CONSTRAINT ratings_pk PRIMARY KEY (id),
    CONSTRAINT ratings_book_fk FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT ratings_member_fk FOREIGN KEY (member_id) REFERENCES members (id) ON DELETE CASCADE ON UPDATE CASCADE
);

/**
 * Create webcontacts table.
 * This table stores the contacts made by the web form.
 */
CREATE TABLE webcontacts (
    creationdate date NOT NULL,
    creationtime time NOT NULL,
    email varchar(100) NOT NULL,
    id int NOT NULL AUTO_INCREMENT,
    member_id int,
    name varchar(100) NOT NULL,
    notes text,
    phone varchar(10) NOT NULL,
    resolved bool NOT NULL DEFAULT false,
    CONSTRAINT webcontacts_pk PRIMARY KEY (id),
    CONSTRAINT webcontacts_member_fk FOREIGN KEY (member_id) REFERENCES members (id) ON DELETE SET NULL ON UPDATE CASCADE
);


/**
 * Create filters table.
 * This table stores filters made by users into views.
 */
CREATE TABLE filters (
    description varchar(50) NOT NULL,
    filters text,
    id int NOT NULL AUTO_INCREMENT,
    name varchar(40) NOT NULL,
    username varchar(50) DEFAULT NULL,
    CONSTRAINT filters_pk PRIMARY KEY (id),
    CONSTRAINT filters_users FOREIGN KEY (username) REFERENCES users (username) ON DELETE CASCADE ON UPDATE CASCADE
);
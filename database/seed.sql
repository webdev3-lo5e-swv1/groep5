-- seed.sql — MBO Cinemas testdata
-- Locatie: database/seed.sql
-- Run dit in phpMyAdmin via het SQL tabblad

USE mbo_cinemas;

-- ── Bioscopen ─────────────────────────────────────────
INSERT INTO bioscopen (naam, stad, adres) VALUES
('MBO Cinemas Amsterdam', 'Amsterdam', 'Damrak 1'),
('MBO Cinemas Rotterdam', 'Rotterdam', 'Coolsingel 40'),
('MBO Cinemas Den Haag',  'Den Haag',  'Spuiplein 150');

-- ── Zalen ─────────────────────────────────────────────
INSERT INTO zalen (bioscoop_id, naam, type) VALUES
(1, 'Zaal 1', 'normaal'),
(1, 'Zaal 2', 'IMAX'),
(2, 'Zaal 1', 'normaal'),
(2, 'Zaal 2', 'normaal'),
(3, 'Zaal 1', 'normaal'),
(3, 'Zaal 2', 'IMAX');

-- ── Stoelen zaal 1 (Amsterdam) — 6 rijen x 10 stoelen ─
INSERT INTO stoelen (zaal_id, rij, nummer, type) VALUES
(1,'A',1,'standaard'),(1,'A',2,'standaard'),(1,'A',3,'standaard'),(1,'A',4,'standaard'),(1,'A',5,'standaard'),
(1,'A',6,'standaard'),(1,'A',7,'standaard'),(1,'A',8,'standaard'),(1,'A',9,'standaard'),(1,'A',10,'standaard'),
(1,'B',1,'standaard'),(1,'B',2,'standaard'),(1,'B',3,'standaard'),(1,'B',4,'standaard'),(1,'B',5,'standaard'),
(1,'B',6,'standaard'),(1,'B',7,'standaard'),(1,'B',8,'standaard'),(1,'B',9,'standaard'),(1,'B',10,'standaard'),
(1,'C',1,'standaard'),(1,'C',2,'standaard'),(1,'C',3,'deluxe'),(1,'C',4,'deluxe'),(1,'C',5,'deluxe'),
(1,'C',6,'deluxe'),(1,'C',7,'deluxe'),(1,'C',8,'deluxe'),(1,'C',9,'standaard'),(1,'C',10,'standaard'),
(1,'D',1,'standaard'),(1,'D',2,'standaard'),(1,'D',3,'deluxe'),(1,'D',4,'deluxe'),(1,'D',5,'deluxe'),
(1,'D',6,'deluxe'),(1,'D',7,'deluxe'),(1,'D',8,'deluxe'),(1,'D',9,'standaard'),(1,'D',10,'standaard'),
(1,'E',1,'standaard'),(1,'E',2,'standaard'),(1,'E',3,'standaard'),(1,'E',4,'standaard'),(1,'E',5,'standaard'),
(1,'E',6,'standaard'),(1,'E',7,'standaard'),(1,'E',8,'standaard'),(1,'E',9,'standaard'),(1,'E',10,'standaard'),
(1,'F',1,'mindervalide'),(1,'F',2,'mindervalide'),(1,'F',3,'standaard'),(1,'F',4,'standaard'),(1,'F',5,'standaard'),
(1,'F',6,'standaard'),(1,'F',7,'standaard'),(1,'F',8,'standaard'),(1,'F',9,'mindervalide'),(1,'F',10,'mindervalide');

-- ── Stoelen zaal 2 (Amsterdam IMAX) — 5 rijen x 10 ───
INSERT INTO stoelen (zaal_id, rij, nummer, type) VALUES
(2,'A',1,'standaard'),(2,'A',2,'standaard'),(2,'A',3,'standaard'),(2,'A',4,'standaard'),(2,'A',5,'standaard'),
(2,'A',6,'standaard'),(2,'A',7,'standaard'),(2,'A',8,'standaard'),(2,'A',9,'standaard'),(2,'A',10,'standaard'),
(2,'B',1,'standaard'),(2,'B',2,'standaard'),(2,'B',3,'deluxe'),(2,'B',4,'deluxe'),(2,'B',5,'deluxe'),
(2,'B',6,'deluxe'),(2,'B',7,'deluxe'),(2,'B',8,'deluxe'),(2,'B',9,'standaard'),(2,'B',10,'standaard'),
(2,'C',1,'deluxe'),(2,'C',2,'deluxe'),(2,'C',3,'deluxe'),(2,'C',4,'deluxe'),(2,'C',5,'deluxe'),
(2,'C',6,'deluxe'),(2,'C',7,'deluxe'),(2,'C',8,'deluxe'),(2,'C',9,'deluxe'),(2,'C',10,'deluxe'),
(2,'D',1,'standaard'),(2,'D',2,'standaard'),(2,'D',3,'standaard'),(2,'D',4,'standaard'),(2,'D',5,'standaard'),
(2,'D',6,'standaard'),(2,'D',7,'standaard'),(2,'D',8,'standaard'),(2,'D',9,'standaard'),(2,'D',10,'standaard'),
(2,'E',1,'mindervalide'),(2,'E',2,'mindervalide'),(2,'E',3,'standaard'),(2,'E',4,'standaard'),(2,'E',5,'standaard'),
(2,'E',6,'standaard'),(2,'E',7,'standaard'),(2,'E',8,'standaard'),(2,'E',9,'mindervalide'),(2,'E',10,'mindervalide');

-- ── Stoelen zaal 3 (Rotterdam) ────────────────────────
INSERT INTO stoelen (zaal_id, rij, nummer, type) VALUES
(3,'A',1,'standaard'),(3,'A',2,'standaard'),(3,'A',3,'standaard'),(3,'A',4,'standaard'),(3,'A',5,'standaard'),
(3,'A',6,'standaard'),(3,'A',7,'standaard'),(3,'A',8,'standaard'),(3,'A',9,'standaard'),(3,'A',10,'standaard'),
(3,'B',1,'standaard'),(3,'B',2,'standaard'),(3,'B',3,'standaard'),(3,'B',4,'standaard'),(3,'B',5,'standaard'),
(3,'B',6,'standaard'),(3,'B',7,'standaard'),(3,'B',8,'standaard'),(3,'B',9,'standaard'),(3,'B',10,'standaard'),
(3,'C',1,'deluxe'),(3,'C',2,'deluxe'),(3,'C',3,'deluxe'),(3,'C',4,'deluxe'),(3,'C',5,'deluxe'),
(3,'C',6,'deluxe'),(3,'C',7,'deluxe'),(3,'C',8,'deluxe'),(3,'C',9,'deluxe'),(3,'C',10,'deluxe'),
(3,'D',1,'standaard'),(3,'D',2,'standaard'),(3,'D',3,'standaard'),(3,'D',4,'standaard'),(3,'D',5,'standaard'),
(3,'D',6,'standaard'),(3,'D',7,'standaard'),(3,'D',8,'standaard'),(3,'D',9,'standaard'),(3,'D',10,'standaard'),
(3,'E',1,'mindervalide'),(3,'E',2,'mindervalide'),(3,'E',3,'standaard'),(3,'E',4,'standaard'),(3,'E',5,'standaard'),
(3,'E',6,'standaard'),(3,'E',7,'standaard'),(3,'E',8,'standaard'),(3,'E',9,'mindervalide'),(3,'E',10,'mindervalide');

-- ── Films (uit movies.json) ───────────────────────────
INSERT INTO films (titel, beschrijving, genre, duur, leeftijd, taal, regisseur, cast, poster) VALUES
('The Shawshank Redemption', 'Twee gevangenen bouwen over een aantal jaren een bijzondere band op, waarin ze troost en uiteindelijke verlossing vinden door middel van goed fatsoen.', 'Drama', 142, '16', 'Engels', 'Frank Darabont', 'Tim Robbins, Morgan Freeman', 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?w=500&q=80'),
('The Godfather', 'De vergrijzende patriarch van een georganiseerde misdaadfamilie draagt de controle over zijn ondergrondse imperium over aan zijn onwillige jongste zoon.', 'Drama, Misdaad', 175, '16', 'Engels', 'Francis Ford Coppola', 'Marlon Brando, Al Pacino', 'https://images.unsplash.com/photo-1533928298208-27ff66555d8d?w=500&q=80'),
('The Dark Knight', 'Wanneer de dreiging die bekend staat als de Joker chaos zaait in Gotham, moet Batman een van de grootste psychologische en fysieke tests van zijn gerechtigheid doorstaan.', 'Actie, Thriller', 152, '16', 'Engels', 'Christopher Nolan', 'Christian Bale, Heath Ledger', 'https://images.unsplash.com/photo-1509248961158-e54f6934749c?w=500&q=80'),
('The Godfather Part II', 'De vroege levensjaren en carrière van Vito Corleone in het New York van de jaren 1920 worden getoond, terwijl zijn zoon Michael zijn grip op het misdaadsyndicaat verstevigt.', 'Drama, Misdaad', 202, '16', 'Engels', 'Francis Ford Coppola', 'Al Pacino, Robert De Niro', 'https://images.unsplash.com/photo-1543536448-d209d2d13a1c?w=500&q=80'),
('12 Angry Men', 'De jury in een moordzaak in New York probeert een unaniem besluit te nemen, waarbij één jurylid de rest dwingt het bewijsmateriaal heroverwegen.', 'Drama', 96, '12', 'Engels', 'Sidney Lumet', 'Henry Fonda, Lee J. Cobb', 'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?w=500&q=80'),
('Schindler\'s List', 'In het door de nazi\'s bezette Polen tijdens de Tweede Wereldoorlog raakt de industrieel Oskar Schindler bezorgd om zijn Joodse arbeiders.', 'Drama, Geschiedenis', 195, '16', 'Engels', 'Steven Spielberg', 'Liam Neeson, Ralph Fiennes', 'https://images.unsplash.com/photo-1440404653325-ab127d49abc1?w=500&q=80'),
('The Lord of the Rings: The Return of the King', 'Gandalf en Aragorn leiden de Wereld van de Mensen tegen het leger van Sauron om de blik af te leiden van Frodo en Sam.', 'Avontuur, Fantasy', 201, '12', 'Engels', 'Peter Jackson', 'Elijah Wood, Viggo Mortensen', 'https://images.unsplash.com/photo-1534447677768-be436bb09401?w=500&q=80'),
('Pulp Fiction', 'De levens van twee bendehuurmoordenaars, een bokser, de gangster-vrouw van een bendeleider verstrengelen zich in vier verhalen.', 'Misdaad, Drama', 154, '16', 'Engels', 'Quentin Tarantino', 'John Travolta, Samuel L. Jackson', 'https://images.unsplash.com/photo-1594909122845-11baa439b7bf?w=500&q=80'),
('The Lord of the Rings: The Fellowship of the Ring', 'Een tamme Hobbit en acht metgezellen beginnen aan een reis om de Ene Ring te vernietigen en zo Midden-aarde te redden.', 'Avontuur, Fantasy', 178, '12', 'Engels', 'Peter Jackson', 'Elijah Wood, Ian McKellen', 'https://images.unsplash.com/photo-1461360370896-922624d12aa1?w=500&q=80'),
('Forrest Gump', 'De geschiedenis van de VS van de jaren 50 tot de jaren 80 ontvouwt zich vanuit het perspectief van een man uit Alabama met een IQ van 75.', 'Drama, Romantiek', 142, '12', 'Engels', 'Robert Zemeckis', 'Tom Hanks, Robin Wright', 'https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?w=500&q=80'),
('Fight Club', 'Een kantoormedewerker met slapeloosheid en een zorgeloze zeepmaker richten een ondergrondse vechtclub op die al snel ontaardt in iets veel groters.', 'Drama, Thriller', 139, '18', 'Engels', 'David Fincher', 'Brad Pitt, Edward Norton', 'https://images.unsplash.com/photo-1485846234645-a62644f84728?w=500&q=80'),
('Inception', 'Een dief die bedrijfsgeheimen steelt via droom-sharing technologie krijgt de omgekeerde taak: het planten van een idee in de geest van een CEO.', 'Actie, Sci-Fi', 148, '12', 'Engels', 'Christopher Nolan', 'Leonardo DiCaprio, Joseph Gordon-Levitt', 'https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?w=500&q=80'),
('The Matrix', 'Wanneer een mooie vreemdeling computerhacker Neo meeneemt naar een grimmige onderwereld, ontdekt hij de schokkende waarheid over zijn realiteit.', 'Actie, Sci-Fi', 136, '16', 'Engels', 'The Wachowskis', 'Keanu Reeves, Laurence Fishburne', 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=500&q=80'),
('Interstellar', 'Wanneer de aarde onleefbaar dreigt te worden, reist een team door een wormgat in de ruimte om het voortbestaan van de mensheid te redden.', 'Avontuur, Sci-Fi', 169, '12', 'Engels', 'Christopher Nolan', 'Matthew McConaughey, Anne Hathaway', 'https://images.unsplash.com/photo-1506703719100-a0f3a48c0f86?w=500&q=80'),
('Goodfellas', 'Het verhaal van Henry Hill en zijn leven in de maffia, zijn relatie met zijn vrouw Karen en zijn maffiapartners.', 'Misdaad, Drama', 146, '16', 'Engels', 'Martin Scorsese', 'Ray Liotta, Robert De Niro', 'https://images.unsplash.com/photo-1578328819058-b69f3a3b0f6b?w=500&q=80'),
('Se7en', 'Twee rechercheurs, een groentje en een veteraan, jagen op een seriemoordenaar wiens motieven gebaseerd zijn op de zeven hoofdzonden.', 'Misdaad, Thriller', 127, '16', 'Engels', 'David Fincher', 'Morgan Freeman, Brad Pitt', 'https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?w=500&q=80'),
('Saving Private Ryan', 'Na de invasie in Normandië gaat een groep soldaten achter de vijandelijke linies om een parachutist te redden wiens broers zijn gesneuveld.', 'Drama, Oorlog', 169, '16', 'Engels', 'Steven Spielberg', 'Tom Hanks, Matt Damon', 'https://images.unsplash.com/photo-1560067174-c5a3a8f37060?w=500&q=80'),
('Parasite', 'Hebzucht en klassendiscriminatie bedreigen de symbiotische relatie tussen de rijke familie Park en de arme familie Kim.', 'Thriller, Drama', 132, '16', 'Koreaans', 'Bong Joon-ho', 'Song Kang-ho, Lee Sun-kyun', 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?w=500&q=80'),
('Gladiator', 'Een voormalige Romeinse generaal zint op wraak tegen de corrupte keizer die zijn familie heeft vermoord en hem tot slaaf heeft gemaakt als gladiator.', 'Actie, Drama', 155, '16', 'Engels', 'Ridley Scott', 'Russell Crowe, Joaquin Phoenix', 'https://images.unsplash.com/photo-1558591710-4b4a1ae0f04d?w=500&q=80'),
('The Departed', 'Een undercoveragent en een mol binnen de politie proberen elkaar te identificeren terwijl ze infiltreren in een Ierse bende in Boston.', 'Misdaad, Thriller', 151, '16', 'Engels', 'Martin Scorsese', 'Leonardo DiCaprio, Matt Damon', 'https://images.unsplash.com/photo-1478760329108-5c3ed9d495a0?w=500&q=80'),
('Whiplash', 'Een veelbelovende jonge drummer schrijft zich in bij een moordend muziekconservatorium waar zijn dromen worden begeleid door een meedogenloze instructeur.', 'Drama, Muziek', 107, '12', 'Engels', 'Damien Chazelle', 'Miles Teller, J.K. Simmons', 'https://images.unsplash.com/photo-1511192336575-5a79af67a629?w=500&q=80'),
('The Shining', 'Een gezin trekt voor de winter in een afgelegen hotel waar een sinistere aanwezigheid de vader aanzet tot geweld.', 'Horror', 146, '16', 'Engels', 'Stanley Kubrick', 'Jack Nicholson, Shelley Duvall', 'https://images.unsplash.com/photo-1509114397022-ed747cca3f65?w=500&q=80'),
('Joker', 'In Gotham City drijft de mentale achteruitgang van de mislukte komiek Arthur Fleck hem tot een leven van nihilistische criminaliteit.', 'Drama, Thriller', 122, '16', 'Engels', 'Todd Phillips', 'Joaquin Phoenix, Robert De Niro', 'https://images.unsplash.com/photo-1553514029-2d185c09ef74?w=500&q=80'),
('Oppenheimer', 'Het biografische verhaal van J. Robert Oppenheimer, die de leiding had over het Manhattanproject en de creatie van de allereerste atoombom.', 'Biografie, Drama', 180, '16', 'Engels', 'Christopher Nolan', 'Cillian Murphy, Emily Blunt', 'https://images.unsplash.com/photo-1447069387593-a5de0862481e?w=500&q=80'),
('Dune: Part Two', 'Paul Atreides verenigt zich met Chani en de Vrijmans terwijl hij op een oorlogspad van wraak zint tegen de samenzweerders die zijn familie hebben vernietigd.', 'Avontuur, Sci-Fi', 166, '12', 'Engels', 'Denis Villeneuve', 'Timothée Chalamet, Zendaya', 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?w=500&q=80'),
('Spider-Man: Across the Spider-Verse', 'Miles Morales keert terug voor een episch avontuur dat hem door het multiversum katapulteert om het op te nemen tegen een nieuwe schurk.', 'Animatie, Actie', 140, '9', 'Engels', 'Joaquim Dos Santos', 'Shameik Moore, Hailee Steinfeld', 'https://images.unsplash.com/photo-1608889174639-414d9f697836?w=500&q=80'),
('Arrival', 'Wanneer mysterieuze ruimteschepen over de hele wereld landen, krijgt een taalkundige de leiding om te communiceren met de buitenaardse wezens.', 'Drama, Sci-Fi', 116, '12', 'Engels', 'Denis Villeneuve', 'Amy Adams, Jeremy Renner', 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=500&q=80'),
('The Lion King', 'Een jonge leeuwenprins ontvlucht zijn koninkrijk na de moord op zijn vader, om vervolgens de betekenis van verantwoordelijkheid en moed te leren.', 'Animatie, Drama', 88, '3', 'Engels', 'Roger Allers', 'Matthew Broderick, Jeremy Irons', 'https://images.unsplash.com/photo-1546182990-dffeafbe841d?w=500&q=80'),
('Spirited Away', 'Tijdens de verhuizing van haar familie dwaalt een 10-jarig meisje af naar een wereld die geregeerd wordt door goden, heksen en geesten.', 'Animatie, Fantasy', 125, '7', 'Japans', 'Hayao Miyazaki', 'Daveigh Chase, Suzanne Pleshette', 'https://images.unsplash.com/photo-1578632767115-351597cf2477?w=500&q=80'),
('Django Unchained', 'Met de hulp van een Duitse premiejager begint een bevrijde slaaf aan een gevaarlijke missie om zijn vrouw te redden.', 'Western, Drama', 165, '16', 'Engels', 'Quentin Tarantino', 'Jamie Foxx, Christoph Waltz', 'https://images.unsplash.com/photo-1533240332313-0db49b439ad3?w=500&q=80'),
('Inglourious Basterds', 'In het door de nazi\'s bezette Frankrijk beraamt een groep Joods-Amerikaanse soldaten een complot om de leiders van het Derde Rijk uit te schakelen.', 'Drama, Oorlog', 153, '16', 'Engels', 'Quentin Tarantino', 'Brad Pitt, Christoph Waltz', 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=500&q=80');

-- ── Voorstellingen — komende 2 weken ─────────────────
-- Film 1 t/m 10 krijgen meerdere voorstellingen per dag

INSERT INTO voorstellingen (film_id, zaal_id, datum, starttijd, eindtijd, prijs) VALUES
-- The Shawshank Redemption (id=1)
(1, 1, CURDATE(), '13:00', '15:22', 12.50),
(1, 1, CURDATE(), '18:00', '20:22', 12.50),
(1, 2, CURDATE(), '21:00', '23:22', 15.00),
(1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '14:00', '16:22', 12.50),
(1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '19:00', '21:22', 12.50),
(1, 3, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '15:00', '17:22', 12.50),

-- The Godfather (id=2)
(2, 1, CURDATE(), '12:00', '14:55', 12.50),
(2, 2, CURDATE(), '17:00', '19:55', 15.00),
(2, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '13:00', '15:55', 12.50),
(2, 3, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '20:00', '22:55', 12.50),

-- The Dark Knight (id=3)
(3, 2, CURDATE(), '14:00', '16:32', 15.00),
(3, 2, CURDATE(), '19:30', '22:02', 15.00),
(3, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '15:00', '17:32', 12.50),
(3, 2, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '20:00', '22:32', 15.00),

-- Inception (id=12)
(12, 2, CURDATE(), '13:30', '16:18', 15.00),
(12, 2, CURDATE(), '20:00', '22:48', 15.00),
(12, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '14:00', '16:48', 12.50),
(12, 3, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '19:00', '21:48', 12.50),

-- Interstellar (id=14)
(14, 2, CURDATE(), '15:00', '17:49', 15.00),
(14, 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '18:00', '20:49', 15.00),
(14, 1, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '14:00', '16:49', 12.50),

-- Parasite (id=18)
(18, 1, CURDATE(), '16:00', '18:12', 12.50),
(18, 3, CURDATE(), '20:30', '22:42', 12.50),
(18, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '15:00', '17:12', 12.50),

-- Oppenheimer (id=23)
(23, 2, CURDATE(), '12:00', '15:00', 15.00),
(23, 2, CURDATE(), '17:00', '20:00', 15.00),
(23, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '13:00', '16:00', 12.50),
(23, 3, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '18:00', '21:00', 12.50),

-- Dune: Part Two (id=24)
(24, 2, CURDATE(), '14:30', '17:16', 15.00),
(24, 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '19:00', '21:46', 15.00),
(24, 1, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '15:00', '17:46', 12.50),

-- Spider-Man: Across the Spider-Verse (id=25)
(25, 1, CURDATE(), '11:00', '13:20', 12.50),
(25, 1, CURDATE(), '15:30', '17:50', 12.50),
(25, 3, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '13:00', '15:20', 12.50),

-- Joker (id=22)
(22, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '17:00', '19:02', 12.50),
(22, 2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '21:00', '23:02', 15.00),
(22, 3, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '16:00', '18:02', 12.50),

-- Gladiator (id=19)
(19, 1, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '13:00', '15:35', 12.50),
(19, 2, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '19:00', '21:35', 15.00),
(19, 3, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '14:00', '16:35', 12.50),

-- Arrival (binnenkort - id=26)
(26, 1, DATE_ADD(CURDATE(), INTERVAL 5 DAY), '15:00', '16:56', 12.50),
(26, 2, DATE_ADD(CURDATE(), INTERVAL 5 DAY), '20:00', '21:56', 15.00),

-- The Lion King (binnenkort - id=27)
(27, 1, DATE_ADD(CURDATE(), INTERVAL 6 DAY), '11:00', '12:28', 10.00),
(27, 1, DATE_ADD(CURDATE(), INTERVAL 6 DAY), '14:00', '15:28', 10.00),

-- Spirited Away (binnenkort - id=28)
(28, 1, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '13:00', '15:05', 10.00),
(28, 2, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '16:00', '18:05', 12.50),

-- Django Unchained (binnenkort - id=29)
(29, 2, DATE_ADD(CURDATE(), INTERVAL 8 DAY), '18:00', '20:45', 15.00),
(29, 1, DATE_ADD(CURDATE(), INTERVAL 9 DAY), '15:00', '17:45', 12.50);

-- ── Test gebruiker ────────────────────────────────────
-- Wachtwoord: Test1234!
INSERT INTO users (voornaam, achternaam, email, wachtwoord, rol) VALUES
('Test', 'Gebruiker', 'test@mbocinemas.nl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'klant'),
('Admin', 'Gebruiker', 'admin@mbocinemas.nl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'medewerker');

-- ── Extra's ───────────────────────────────────────────
INSERT INTO extras (naam, prijs) VALUES
('Popcorn (groot)', 5.50),
('Popcorn (klein)', 3.50),
('Cola (groot)', 4.00),
('Cola (klein)', 2.50),
('Water', 2.00),
('Nachos', 6.00),
('Hotdog', 5.00);
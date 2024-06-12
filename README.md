# newsweek
Za kolegij Programiranje Web Aplikacija

Postati ću link na youtube ovdje, imam problema sa youtubeom.

Instalirati docker. Pokrenuti docker compose.
Spojiti se na localhost:8080.
Ukoliko je port zauzet, promijeniti port u compose fileu.
Nema potrebe kreirati tablice, sve se samo odradi.
Na žalost nisam uspio normalno promijeniti prava za kreiranje fileova.
Zato se treba spojiti na container:
$docker exec -it newsweek bash
$chmod -R 777 ./*
Ovo je više nekakav hotfix nego nešto pametno, ali ovako image upload radi.

Budući da je docker volume bindan u ovaj folder, trebao bi nastati db_data folder.
Tu je samo data iz mysql baze koja se podigne u containeru.


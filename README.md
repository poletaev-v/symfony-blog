# symfony-blog
Новостной сайт на Symfony Framework.

## Системные требования

---
- *[make](https://www.gnu.org/software/make/)*;
- *[docker](https://docs.docker.com/engine/install/ubuntu/)*;
- *[docker-compose](https://docs.docker.com/compose/install/)*;

## Использование

Для запуска приложения, выполните команду в терминале:
```
make start
```
- Приложение обслуживается **на порту: 8000** с базой данных (PostgreSQL) **на порту: 5432**.
- Публичная часть проекта имеет следующие маршруты:
- "/" - Главная страница;
- "/page/{page<[1-9]\d*>}" - Пагинация на главной страницы;
- "/news/{slug}" - Детальная страница новости;
---
- Административная часть проекта имеет следующий маршрут:
- "/admin"
- **Примечание:** email и пароль для входа в административную панель:
- email: ***admin@test.ru***
- password: ***12345qwerty***

---
- **Конфигурация веб-сервера** находится по пути ***"/config/nginx/conf.d/default.conf"***;
- **Конфигурация php** находится по пути ***/config/php/local.ini***;
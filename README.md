# HomeQuest Back-end v 0.1.2
**API:**
| Verb | Path                 | Action   | Description | Params |
|------|----------------------|----------|-------------|--------|
| GET  | /                    | Closure  |            |
| POST | /login               | login    | войти в систему | login, password |
| POST | /logout              | logout   | выйти из системы | |
| POST | /gang                | store    | создать банду | name, hero_id |
| POST | /gang/join           | join     | Добавить героя в банду | code |
| GET  | /gang/{id}           | show     | полчить данные о банде | |
| GET  | /gang/{id}/invite    | invite   | создать код приглашения в банду | |
| PUT  | /gang/{id}           | update   | изменить данные о банде | name |
| POST | /hero                | store    | создать героя | login, name, email, gangId, password |
| PUT  | /hero                | update   | изменить данные о герое | login, name, avatar |
| GET  | /hero                | showSelf | показать данные о авторизованном герое | |
| GET  | /hero/{id}           | show     | показать данные о герое | |
| POST | /quest               | store    | создать квест | title, description, reward, gang_id |
| GET  | /quest/{id}          | show     | показать данные квеста | |
| PUT  | /quest/{id}          | update   | обновить данные квеста | title, description, reward |
| PUT  | /quest/{id}/progress | progress | взять квест | |
| PUT  | /quest/{id}/pending  | pending  | закончить квест | |
| PUT  | /quest/{id}/complete | complete | подтвердить выполнение квеста | |
| PUT  | /quest/{id}/decline  | decline  | отменить квест | |
| PUT  | /quest/{id}/reopen   | reopen   | открыть квест заново | |
| PUT  | /quest/{id}/delete   | delete   | удалить квест | |
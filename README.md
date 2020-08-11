# HomeQuest Back end
**API:**
| Verb | Path                 | Action   | Description | Params |
|------|----------------------|----------|-------------|--------|
| GET  | /                    | Closure  |            |
| POST | /login               | login    | создать банду | login, password |
| POST | /logout              | logout    | создать банду | |
| POST | /gang                | store    | создать банду | name, hero_id |
| GET  | /gang/{id}           | show     | полчить данные о банду | |
| GET  | /gang/{id}/heroes    | heroes   | получить список героев в банде | |
| PUT  | /gang/{id}           | update   | изменить данные о банде | name |
| PUT  | /gang/{id}/join      | join     | Добавить героя в банду | hero_id |
| POST | /hero                | store    | создать героя | login, name, avatar, gangId |
| PUT  | /hero/{id}           | update   | изменить данные о герое | login, name, avatar |
| GET  | /hero/{id}           | show     | показать данные о герое | |
| POST | /quest               | store    | создать квест | title, description, reward, hero_id |
| PUT  | /quest/{id}          | update   | обновить данные квеста | title, description, reward |
| PUT  | /quest/{id}/progress | progress | взять квест | heroId |
| GET  | /quest/{id}          | show     | показать данные квеста | |
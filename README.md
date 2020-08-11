# HomeQuest Back end
**API:**
| Verb | Path                 | Action   | Description | Params |
|------|----------------------|----------|-------------|--------|
| GET  | /                    | Closure  |            |
| POST | /gang                | store    | создать банду | name |
| GET  | /gang/{id}           | show     | полчить данные о банду | |
| GET  | /gang/{id}/heroes    | heroes   | получить список героев в банде | |
| PUT  | /gang/{id}           | update   | изменить данные о банде | name |
| POST | /hero                | store    | создать героя | login, name, avatar, gangId |
| PUT  | /hero/{id}           | update   | изменить данные о герое | login, name, avatar |
| GET  | /hero/{id}           | show     | показать данные о герое | |
| GET  | /hero/{id}/avaliableQuests | showWithAvaliableQuests | показать все квесты доступные для героя | |
| GET  | /hero/{id}/createdQuests | showWithCreatedQuests | показать все созданные героем квесты | |
| POST | /quest               | store    | создать квест | title, description, reward, heroId |
| PUT  | /quest/{id}          | update   | обновить данные квеста | title, description, reward |
| PUT  | /quest/{id}/progress | progress | взять квест | heroId |
| GET  | /quest/{id}          | show     | показать данные квеста | |
# Consultório Alura

Simples API para busca de médicos e suas especialidades.

## Endpoints:

- GET /medicos: Buscar todos os médicos
- POST /medicos: Inserir um novo médico
- PUT /medicos/{id}: Atualiza o médico com o ID informado
- DELETE /medicos/{id}: Remove o médico com o ID informado
- GET /especialidades: Buscar todas as especialidades
- POST /especialidades: Inserir uma nova especialidade
- PUT /especialidades/{id}: Atualiza a especialidade com o ID informado
- DELETE /especialidades/{id}: Remove a especialidade com o ID informado
- GET /especialidades/{id}/medicos: Busca os médicos que tenham a especialidade com o ID informado

## Subir os ambientes 
```
docker-compose up
docker exec -it alura_php php bin/console doctrine:schema:create
```

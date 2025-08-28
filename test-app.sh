#!/bin/bash

API_URL="http://localhost:8000/api/v1"

echo "=== Criando admin (ID:1) ==="
curl -s -X POST "$API_URL/users" \
  -H "X-User-Id: 1" \
  -H "Content-Type: application/json" \
  -d '{"name":"Admin","email":"admin@crm.test","password":"secret","role":"admin","status":"active"}'
echo -e "\n✔ Admin criado\n"

echo "=== Listando usuários (como admin) ==="
curl -s -H "X-User-Id: 1" "$API_URL/users"
echo -e "\n✔ Usuários listados\n"

echo "=== Criando agente (ID:2) ==="
curl -s -X POST "$API_URL/users" \
  -H "X-User-Id: 1" \
  -H "Content-Type: application/json" \
  -d '{"name":"Agente","email":"agente@crm.test","password":"123456","role":"agent","status":"active"}'
echo -e "\n✔ Agente criado\n"

echo "=== Criando lead como agente ==="
curl -s -X POST "$API_URL/leads" \
  -H "X-User-Id: 2" \
  -H "Content-Type: application/json" \
  -d '{"name":"Cliente X","email":"cliente@x.com","company_name":"Empresa X","budget":10000}'
echo -e "\n✔ Lead criado\n"

echo "=== Listando leads ==="
curl -s -H "X-User-Id: 2" "$API_URL/leads"
echo -e "\n✔ Leads listados\n"

echo "=== Atualizando lead (ID:1) como dono ==="
curl -s -X PATCH "$API_URL/leads/1" \
  -H "X-User-Id: 2" \
  -H "Content-Type: application/json" \
  -d '{"status":"in_progress"}'
echo -e "\n✔ Lead atualizado\n"

echo "=== Excluindo lead (ID:1) como dono ==="
curl -s -X DELETE "$API_URL/leads/1" \
  -H "X-User-Id: 2"
echo -e "\n✔ Lead excluído\n"

echo "=== Excluindo agente (ID:2) como admin ==="
curl -s -X DELETE "$API_URL/users/2" \
  -H "X-User-Id: 1"
echo -e "\n✔ Agente excluído\n"

echo "=== Fluxo finalizado com sucesso 🚀 ==="

# Arquitetura da Plataforma MEIControl

## Stack
- Laravel 12
- Livewire 3
- Alpine.js
- Tailwind CSS
- MySQL (Laragon)

## Modulos
- Dashboard executivo com KPIs financeiros e operacionais
- Compras (cadastro, itens, documentos e impacto no estoque)
- Vendas (cadastro, itens, documentos e impacto no estoque)
- Estoque (saldo por produto e movimentacoes)
- Relatorios por periodo
- Contas a pagar e contas a receber

## Estrutura de diretorios
- `app/Models`: entidades e relacionamentos
- `app/Livewire`: componentes reativos por modulo
- `app/Services`: regras de negocio (`PurchaseService`, `SaleService`, `InventoryService`, `DashboardService`)
- `app/Repositories`: abstrações de acesso a dados
- `app/Policies`: autorizacao por recurso
- `app/Http/Requests`: validacoes de entrada
- `database/migrations`: modelagem completa do banco
- `database/seeders`: papeis, permissoes e usuario admin inicial
- `resources/views/pages`: paginas administrativas
- `resources/views/livewire`: telas por modulo

## Fluxos centrais
- Compra criada -> itens gravados -> estoque incrementado -> movimentacao de estoque registrada.
- Venda criada -> itens gravados -> estoque decrementado -> movimentacao de estoque registrada.
- Dashboard calcula faturamento, lucro estimado, contas e alertas de estoque.

## Proximos passos recomendados
- Implementar upload persistente em `attachments` para compras e vendas.
- Adicionar exportacao PDF/Excel em relatorios.
- Integrar notificacoes em tempo real (eventos/broadcast).
- Criar trilha de auditoria detalhada com visualizador de logs.

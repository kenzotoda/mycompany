<?php

namespace App\Support;

/**
 * Mapeamento dos campos por tela.
 *
 * Tipos:
 * - text: texto livre
 * - email: e-mail
 * - select: lista de opções
 * - date: data
 * - file: upload
 * - document_cpf / document_cnpj / phone: somente números (com máscara)
 * - decimal: números com casas decimais
 * - integer: números inteiros
 */
class FormFields
{
    public static function screens(): array
    {
        return [
            'produtos.cadastro' => [
                'name' => ['label' => 'Nome do produto', 'type' => 'text', 'required' => true],
                'sku' => ['label' => 'SKU', 'type' => 'text', 'required' => false],
            ],
            'fornecedores' => [
                'name' => ['label' => 'Nome do fornecedor', 'type' => 'text', 'required' => true],
                'document' => ['label' => 'CNPJ', 'type' => 'document_cnpj', 'required' => true],
                'phone' => ['label' => 'Telefone', 'type' => 'phone', 'required' => false],
                'email' => ['label' => 'E-mail', 'type' => 'email', 'required' => false],
            ],
            'clientes' => [
                'name' => ['label' => 'Nome do cliente', 'type' => 'text', 'required' => true],
                'document_type' => ['label' => 'Tipo de documento', 'type' => 'select', 'options' => ['cpf', 'cnpj'], 'required' => false],
                'document' => ['label' => 'CPF ou CNPJ', 'type' => 'document_cpf_cnpj', 'required' => false],
                'phone' => ['label' => 'Telefone', 'type' => 'phone', 'required' => false],
                'email' => ['label' => 'E-mail', 'type' => 'email', 'required' => false],
            ],
            'compras.nova' => [
                'supplier_id' => ['label' => 'Fornecedor', 'type' => 'select', 'required' => true],
                'purchase_date' => ['label' => 'Data da compra', 'type' => 'date', 'required' => true],
                'payment_method' => ['label' => 'Forma de pagamento', 'type' => 'select', 'required' => true, 'options' => PaymentMethods::values()],
                'items.product_id' => ['label' => 'Produto', 'type' => 'select', 'required' => true],
                'items.quantity' => ['label' => 'Quantidade', 'type' => 'integer', 'required' => true],
                'items.unit_price' => ['label' => 'Valor unitário', 'type' => 'decimal', 'required' => true],
                'notes' => ['label' => 'Observações', 'type' => 'text', 'required' => false],
                'documents' => ['label' => 'Anexos', 'type' => 'file', 'required' => false],
            ],
            'vendas.nova' => [
                'customer_id' => ['label' => 'Cliente', 'type' => 'select', 'required' => true],
                'sale_date' => ['label' => 'Data da venda', 'type' => 'date', 'required' => true],
                'payment_method' => ['label' => 'Forma de pagamento', 'type' => 'select', 'required' => true, 'options' => PaymentMethods::values()],
                'installments' => ['label' => 'Parcelas', 'type' => 'integer', 'required' => false, 'when' => 'payment_method=credito'],
                'items.product_id' => ['label' => 'Produto', 'type' => 'select', 'required' => true],
                'items.quantity' => ['label' => 'Quantidade', 'type' => 'integer', 'required' => true],
                'items.unit_price' => ['label' => 'Valor unitário', 'type' => 'decimal', 'required' => true],
                'notes' => ['label' => 'Observações', 'type' => 'text', 'required' => false],
                'documents' => ['label' => 'Anexos', 'type' => 'file', 'required' => false],
            ],
            'auth.login' => [
                'email' => ['label' => 'E-mail', 'type' => 'email', 'required' => true],
                'password' => ['label' => 'Senha', 'type' => 'text', 'required' => true],
            ],
            'auth.register' => [
                'name' => ['label' => 'Nome', 'type' => 'text', 'required' => true],
                'email' => ['label' => 'E-mail', 'type' => 'email', 'required' => true],
                'password' => ['label' => 'Senha', 'type' => 'text', 'required' => true],
                'password_confirmation' => ['label' => 'Confirmar senha', 'type' => 'text', 'required' => true],
            ],
        ];
    }

    public static function isNumericOnlyType(string $type): bool
    {
        return in_array($type, [
            'document_cpf',
            'document_cnpj',
            'document_cpf_cnpj',
            'phone',
            'decimal',
            'integer',
        ], true);
    }
}

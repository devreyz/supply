<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GondolaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário de teste
        $user = User::create([
            'name' => 'Admin Teste',
            'email' => 'admin@godola.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Criar empresa de teste
        $company = Company::create([
            'name' => 'Supermercado Teste LTDA',
            'document' => '12.345.678/0001-90',
            'owner_id' => null,
        ]);

        // Associar usuário à empresa como owner
        $user->companies()->attach($company->id, ['role' => 'owner']);
        $user->update(['current_company_id' => $company->id]);

        // Criar categorias
        $alimentos = Category::create([
            'company_id' => $company->id,
            'name' => 'Alimentos',
            'order' => 1,
        ]);

        $bebidas = Category::create([
            'company_id' => $company->id,
            'name' => 'Bebidas',
            'order' => 2,
        ]);

        $limpeza = Category::create([
            'company_id' => $company->id,
            'name' => 'Limpeza',
            'order' => 3,
        ]);

        // Subcategorias
        Category::create([
            'company_id' => $company->id,
            'name' => 'Arroz e Feijão',
            'parent_id' => $alimentos->id,
            'order' => 1,
        ]);

        // Criar fornecedores
        $fornecedores = [
            ['name' => 'Atacadão Norte', 'rating' => 4, 'phone' => '(11) 98765-4321'],
            ['name' => 'Distribuidora Silva', 'rating' => 5, 'phone' => '(11) 91234-5678'],
            ['name' => 'Martins Atacado', 'rating' => 4, 'phone' => '(11) 99876-5432'],
            ['name' => 'Tambasa', 'rating' => 3, 'phone' => '(11) 92345-6789'],
            ['name' => 'Assaí Atacadista', 'rating' => 5, 'phone' => '(11) 93456-7890'],
        ];

        foreach ($fornecedores as $fornecedor) {
            Supplier::create([
                'company_id' => $company->id,
                'name' => $fornecedor['name'],
                'rating' => $fornecedor['rating'],
                'phone' => $fornecedor['phone'],
            ]);
        }

        // Criar produtos
        $produtos = [
            ['name' => 'Arroz Tio João 5kg', 'category_id' => $alimentos->id, 'unit' => 'CX', 'ean' => '7891234560001'],
            ['name' => 'Feijão Carioca Kicaldo 1kg', 'category_id' => $alimentos->id, 'unit' => 'FD', 'ean' => '7891234560002'],
            ['name' => 'Óleo de Soja Lisa 900ml', 'category_id' => $alimentos->id, 'unit' => 'UN', 'ean' => '7891234560003'],
            ['name' => 'Açúcar União 1kg', 'category_id' => $alimentos->id, 'unit' => 'FD', 'ean' => '7891234560004'],
            ['name' => 'Café Pilão 500g', 'category_id' => $alimentos->id, 'unit' => 'UN', 'ean' => '7891234560005'],
            ['name' => 'Leite Integral Piracanjuba 1L', 'category_id' => $bebidas->id, 'unit' => 'UN', 'ean' => '7891234560006'],
            ['name' => 'Refrigerante Coca-Cola 2L', 'category_id' => $bebidas->id, 'unit' => 'UN', 'ean' => '7891234560007'],
            ['name' => 'Sabão em Pó OMO 1kg', 'category_id' => $limpeza->id, 'unit' => 'UN', 'ean' => '7891234560008'],
            ['name' => 'Detergente Ypê 500ml', 'category_id' => $limpeza->id, 'unit' => 'UN', 'ean' => '7891234560009'],
            ['name' => 'Água Sanitária 1L', 'category_id' => $limpeza->id, 'unit' => 'UN', 'ean' => '7891234560010'],
        ];

        foreach ($produtos as $produto) {
            $ean = $produto['ean'];
            unset($produto['ean']);

            $product = Product::create([
                'company_id' => $company->id,
                ...$produto,
                'is_active' => true,
                'min_stock' => 10,
            ]);

            // Adicionar código EAN
            $product->codes()->create([
                'code' => $ean,
                'type' => 'ean',
            ]);
        }

        $this->command->info('✅ Dados de teste criados com sucesso!');
        $this->command->info('📧 Email: admin@godola.test');
        $this->command->info('🔑 Senha: password');
    }
}

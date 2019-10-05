<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EspecialidadesWebTest extends WebTestCase
{
    public function testGaranteQueRequisicaoFalhaSemAutenticacao()
    {
        $client = static::createClient();
        $client->request('GET', '/especialidades');

        self::assertEquals(
            401,
            $client->getResponse()->getStatusCode()
        );
    }

    public function testGaranteQueEspecialidadesSaoListadas()
    {
        $client = static::createClient();
        $token = $this->login($client);
        $client->request('GET', '/especialidades', [], [], [
            'HTTP_AUTHORIZATION' => "Bearer $token"
        ]);

        $resposta = json_decode($client->getResponse()->getContent());
        self::assertTrue($resposta->success);
    }

    public function testInsereEspecialidade()
    {
        $client = static::createClient();
        $token = $this->login($client);
        $client->request('POST', '/especialidades', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => "Bearer $token"
        ], json_encode([
            'descricao' => 'Teste'
        ]));

        self::assertEquals(201, $client->getResponse()->getStatusCode());
    }

    private function login(KernelBrowser $client): string
    {
        $client->request(
            'POST',
            '/login',
            [],
            [],
            [
            'CONTENT_TYPE' => 'application/json'
            ],
            json_encode([
                'username' => 'username',
                'password' => 'password'
            ])
        );

        return json_decode($client->getResponse()->getContent())
            ->access_token;
    }

    public function testHtmlEspecialidades()
    {
        $client = self::createClient();
        $client->request('GET', '/especialidades_html');

        $this->assertSelectorTextContains('h1', 'Especialidades');
        $this->assertSelectorExists('.especialidade');
    }
}

<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DadosGeraisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $createdAt = Carbon::now();

        \App\Models\Core\TermosDeUsoModel::insert([
            ['id' => 1, 'texto' => '<p class="ql-align-center"><strong style="color: rgb(83, 129, 53);">Sistema de Assistência Técnica e Extensão Rural e Ambiental - SisRural</strong></p><p><span style="color: rgb(38, 38, 38);">Última Atualização 04/setembro/2020</span></p><p><br></p><p class="ql-align-justify"><span style="color: black;">O Sistema de Assistência Técnica e Extensão Rural e Ambiental - SisRural é uma solução composta por aplicação </span><em style="color: black;">web</em><span style="color: black;"> e aplicativo&nbsp;para dispositivos móveis, desenvolvida em código aberto para estruturar e instrumentalizar as políticas públicas da Prefeitura de São </span>Paulo <span style="color: black;">de </span><a href="https://www.gov.br/agricultura/pt-br/assuntos/ater/o-que-e-assistencia-tecnica" target="_blank" style="color: rgb(17, 85, 204);">Assistência Técnica e Extensão Rural</a><span style="color: black;"> (ATER) e</span>xecutadas pelas <a href="https://www.prefeitura.sp.gov.br/cidade/secretarias/desenvolvimento/abastecimento/agricultura_urbana/index.php?p=153588" target="_blank" style="color: rgb(17, 85, 204);">CAEs</a>(Casas de Agricultura Ecológicas)<span style="color: black;">, </span><a href="https://www.prefeitura.sp.gov.br/cidade/secretarias/meio_ambiente/projetos_e_programas/index.php?p=286787" target="_blank" style="color: rgb(17, 85, 204);">Pagamentos por Serviços Ambientais (PSA) e Protocolos</a><span style="color: black;">, além de facilitar a gestão, possibilitando o monitoramento do desenvolvimento rural sustentável e solidário.&nbsp;</span></p><p class="ql-align-justify"><span style="color: black;">O SisRural deverá ser utilizado somente para coletar, armazenar e gerenciar dados cadastrais de produtores, das unidades de produção agropecuária, dos sistemas de produção, dados sobre os atendimentos, recomendações técnicas, aplicação de formulários e protocolos para programas de assistência técnica e extensão rural, certificações agroecológicas e pagamento por serviços ambientais destinados aos agricultores e zonas rurais, além de permitir a geração&nbsp;e acompanhamento</span> de planos de ação individuais e coletivos e <span style="color: black;">de indicadores e relatórios para acompanhamento d</span>as atividades técnicas e<span style="color: black;"> das políticas. </span><strong style="color: black;">&nbsp;</strong></p><p class="ql-align-justify"><span style="color: black;">Este termo de uso regula o&nbsp;uso do SisRural e das informações nele armazenadas.</span></p><p class="ql-align-justify"><span style="color: black;">&nbsp;</span></p><p class="ql-align-justify"><strong style="color: rgb(83, 129, 53);">Quem pode utilizar o SisRural:</strong><span style="color: black;">somente usuários cadastrados podem utilizar o SisRural. Técnicos de Assistência Técnica Rural, associados a CAEs, CATIs ou outras instituições autorizadas, além de Gestores de Política Públicas autorizados para a utilização do sistema.&nbsp;A aprovação e liberação de acesso </span>está<span style="color: black;"> sob responsabilidade do Projeto Ligue os Pontos, </span>da Secretaria Municipal de Desenvolvimento Urbano de São Paulo.</p><p class="ql-align-justify"><span style="color: black;">&nbsp;</span></p><p class="ql-align-justify"><strong style="color: rgb(83, 129, 53);">Compartilhamento de usuário e senha:</strong><span style="color: black;">não é permitido o compartilhamento de usuário e senha com terceiros ou outros usuários com perfis diferentes, podendo responder legalmente em caso de não observância desta regra</span>. O usuário declara e garante que é responsável por todo o Conteúdo que contribuir, de qualquer maneira, para os Serviços.</p><p class="ql-align-justify"><strong style="color: rgb(83, 129, 53);">&nbsp;</strong></p><p class="ql-align-justify"><strong style="color: rgb(83, 129, 53);">Informações coletadas e armazenadas pelo SisRural</strong></p><p class="ql-align-justify"><strong style="color: rgb(127, 127, 127);">Informações pessoais para cadastro dos Usuários do sistema</strong><span style="color: black;">: as informações pessoais coletadas são utilizadas para cadastro dos usuários e são informações internas ao sistema, não são divulgadas ou compartilhadas. São informações cadastrais como nome, CPF, telefone, e-mail, informações necessárias para identificação do usuário. </span></p><p class="ql-align-justify"><strong style="color: rgb(127, 127, 127);">Dados dos produtores e unidades produtivas</strong><span style="color: black;">: As informações de produtores e unidades produtivas são cadastradas no SisRural pelos usuários do sistema. De acordo com suas permissões, os u</span>suários<span style="color: black;"> poderão cadastrar novos produtores e unidades produtivas, registrar visitas </span>de<span style="color: black;"> campo, aplicar formulários e protocolos para programas de assistência técnica e extensão rural, certificações agroecológicas e pagamento por serviços ambientais, gerar recomendações, elaborar planos de ação, fazer análises com base nos dados disponíveis, além de acessar relatórios de gestão. </span>Produtores ou outras pessoas com dados registrados no SisRural podem autorizar a divulgação pública de suas informações comerciais via técnico cadastrado no sistema.</p><p class="ql-align-justify">Estão vedados os usos de dados identificáveis individualmente não autorizados para fins distintos do que a assistência técnica e extensão rural, certificações agroecológicas e políticas de pagamento de serviços ambientais, em especial aqueles relacionados a atividades de fiscalização e correlatas. O uso por outras políticas públicas poderá ser autorizado pelos responsáveis pela gestão do sistema, desde que sem caráter de fiscalização.</p><p class="ql-align-justify"><span style="color: black;">&nbsp;</span></p><p><strong style="color: rgb(56, 86, 35);">Envio de comunicação para Produtores</strong></p><p><br></p><p class="ql-align-justify"><span style="color: black;">O SisRura</span>l poderá, a critério do técnico, <span style="color: black;">envi</span>ar<span style="color: black;"> e-mails ou outra forma de comunicação para Produtores, com informações contendo os dados e informações registradas, planos de a</span>ção e outros instrumentos gerados, com o objetivo de facilitar o trabalho desenvolvido em conjunto.<span style="color: black;"> Informações de um produtor e/ou unidade produtiva não podem ser encaminhadas para terceiros sem prévia autorização expressa do </span>mesmo<span style="color: black;">. Somente a/o produtor/a, conforme os dados de contato registrados no sistema, </span>ou coprodutores<span style="color: black;"> registrados, podem receber estas comunicações.</span></p><p><br></p><p><strong style="color: rgb(56, 86, 35);">Uso inapropriado das informações ou do sistema e seu aplicativo</strong></p><p><br></p><p class="ql-align-justify"><span style="color: black;">Consideramos uso inapropriado:</span></p><p class="ql-align-justify"><span style="color: black;">&nbsp;- Uso&nbsp;para propósitos ilegais.</span></p><p class="ql-align-justify"><span style="color: black;">- Alteração de informações e uso dessas informações adulteradas para qualquer fim.</span></p><p class="ql-align-justify"><span style="color: black;">- Modificar endereços de máquinas, de rede ou utilizar endereços de correio eletrônico de outros na tentativa de responsabilizar terceiros ou ocultar a identidade ou autoria,&nbsp;destruir ou corromper dados e informações registrados no </span>sistema<span style="color: black;">.</span></p><p class="ql-align-justify"><span style="color: black;">- Alterar informações relacionadas aos produtores e suas unidades produtivas sem</span> confirmar a precisão e veracidade das informações coletadas.</p><p class="ql-align-justify"><span style="color: black;">- Uso de qualquer tipo de processamento automatizado (robô) para acesso em massa às informações do SisRural para consulta, extração, alteração, exclusão de dados ou qualquer outra finalidade sem prévia autorização do</span>s responsáveis pela gestão do sistema<span style="color: black;">.</span></p><p><span style="color: black;">- Praticar atos que prejudiquem o SisRural, como por exemplo incluir vírus, trojans, malware, worm, bot, backdoor, spyware, rootkit, ou qualquer outro meio que possa corromper ou destruir as informações e funcionamento do sistema</span>.</p><p><span style="color: black;">- Uso de informações de produtores ou unidades produtivas para benefício próprio (usuário) ou para divulgação ou compartilhamento com terceiros</span>.</p><p class="ql-align-justify"><span style="color: black;">- Violar copyright ou direito autoral alheio reproduzindo material sem prévia autorização ou desrespeitando os termos de uso estabelecido pelo SisRural.</span></p><p><strong style="color: rgb(83, 129, 53);">&nbsp;</strong></p><p><strong style="color: rgb(83, 129, 53);">Responsabilidades do Usuário</strong></p><p>- Os usuários são responsáveis pelas informações e fotos fornecidas para registro no SisRural, devendo tomar todos os cuidados para a inserção apenas de dados verificados e confiáveis, especialmente quando forem realizadas atualizações de cadastro, caso em que um erro pode gerar perda definitiva de informação e prejuízos a todos os demais usuários do sistema e políticas públicas desenvolvidas por meio do SisRural.</p><p>- Danos causados pelo uso indevido do SisRural a terceiros ou ao Sistema.</p><p>- Usuário é responsável por prover seu acesso seguro a internet.</p><p><strong style="color: rgb(83, 129, 53);">Legislação Aplicável</strong></p><p>Este Termo de Uso é regido e interpretado pela Legislação Brasileira.</p><p><br></p><p><strong style="color: rgb(83, 129, 53);">LGPD – Lei Geral de Proteção de Dados Pessoais (LGPD) </strong></p><p>Conforme Artigo 5º. Da LGPD, incisos VI, VII, VIII e IX, o tratamento dos dados é de responsabilidade do Controlador, e são realizadas pelo Operador.&nbsp;O controlador e o Operador&nbsp;são denominados de&nbsp;agentes de tratamento.</p><p>No SisRural, os agentes de tratamento estão representados pelos usuários do sistema para a realização dos serviços de assistência técnica rural e outras políticas públicas, conforme autorização concedida pela equipe do&nbsp;Projeto Ligue os Pontos, da Secretaria Municipal de Desenvolvimento Urbano de São Paulo, que neste caso representa o Controlador.</p><p>Os profissionais, ao serem designados para assumir o papel de controlador, operador ou encarregado, são notificados de suas responsabilidades antes de aceitarem e assumirem as atividades.</p><p>Todos os dados pessoais de Produtores e dos usuários dos sistema que forem coletados e armazenados pela Sisrural seguem as determinações da LGPD, Lei Nº 13.709/2018 quanto ao seu tratamento de privacidade e são necessários para a execução e atendimento das políticas públicas para as quais o sistema foi desenvolvido.</p><p><br></p><p><strong style="color: rgb(83, 129, 53);">Alterações do Termo de Uso</strong></p><p>Este Termo de Uso poderá ser atualizado sempre que for necessário para melhoria e aperfeiçoamento, sem aviso prévio. Recomendamos visitar com frequência.&nbsp;</p>']
        ]);

        \App\Models\Core\AssistenciaTecnicaTipoModel::insert([
            ['nome' => 'Pública', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Privada', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Outra', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\GeneroModel::insert([
            ['nome' => 'Feminino', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Masculino', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Outro', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Prefiro não dizer', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\EtiniaModel::insert([
            ['nome' => 'Parda', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Amarela', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Preta', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Indígena', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Branca', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Não deseja informar', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\RelacaoModel::insert([
            ['nome' => 'Sócio/a', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Familiar', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Coproprietário/a', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Permanente', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Temporária', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Diarista', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Meeiro/a', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Outra', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\InstalacaoTipoModel::insert([
            ['nome' => 'Instalações Produtivas', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Máquinas, Tratores e Veículos', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Implementos de Tração Mecânica', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Irrigação', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\SoloCategoriaModel::insert([
            ['id' => 1, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Olericultura - Folhosas e inflorescências', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 2, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Olericultura - Raízes/Tubérculos', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 3, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Olericultura - Legumes', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 4, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Monocultura - Fruticultura/café', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 5, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Ornamentais', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 6, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Olericultura - Aromáticas/ Condimentares/ Medicinais', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 7, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Fungicultura', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 8, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Cultivo protegido - Mudas', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 9, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Silvicultura', 'created_at' => $createdAt, 'updated_at' => $createdAt],

            ['id' => 10, 'tipo' => 'geral', 'tipo_form' => 'hectares', 'nome' => 'Pastagens', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 11, 'tipo' => 'geral', 'tipo_form' => 'hectares', 'nome' => 'Pousio', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 12, 'tipo' => 'geral', 'tipo_form' => 'hectares', 'nome' => 'Vegetação nativa', 'created_at' => $createdAt, 'updated_at' => $createdAt],

            ['id' => 13, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Pomar/ horta doméstica', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 14, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Sistemas agroflorestais (SAF)', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            // ['id' => 15, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 16, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Olericultura - Frutas', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 17, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Cultura anual', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 18, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Monocultura', 'created_at' => $createdAt, 'updated_at' => $createdAt],

            ['id' => 19, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'ILPF - Integração Lavoura-Pecuária-Floresta', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 20, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Consorcio - outros', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            // ['id' => 21, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 22, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Cultivo protegido - Fungicultura', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 23, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Cultivo protegido - Olericultura', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 24, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Cultivo protegido - Outros', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 25, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Aves de corte', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 26, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Aves de postura', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 27, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Suínos', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 28, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Animais aquáticos', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 29, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Bovinos de leite', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 30, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Bovinos de corte', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 31, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Meliponicultura', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 32, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Apicultura', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 33, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Caprinos/Ovinos', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 34, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Piscicultura', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 35, 'tipo' => 'geral', 'tipo_form' => 'todos', 'nome' => 'Animais Outros', 'created_at' => $createdAt, 'updated_at' => $createdAt],

            ['id' => 40, 'tipo' => 'outros', 'tipo_form' => null, 'nome' => 'Turismo', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 41, 'tipo' => 'outros', 'tipo_form' => null, 'nome' => 'Turismo Escolar', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 42, 'tipo' => 'outros', 'tipo_form' => null, 'nome' => 'Lazer', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 43, 'tipo' => 'outros', 'tipo_form' => null, 'nome' => 'Extrativismo', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 44, 'tipo' => 'outros', 'tipo_form' => null, 'nome' => 'Mineração', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 45, 'tipo' => 'outros', 'tipo_form' => null, 'nome' => 'Outros', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\TipoPosseModel::insert([
            ['id' => 1, 'nome' => 'Arrendatário/a', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 2, 'nome' => 'Assentado/a', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 3, 'nome' => 'Proprietário/a', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 4, 'nome' => 'Comodatário/a', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 5, 'nome' => 'Parceiro/a', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 6, 'nome' => 'Usufrutuário/a', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 7, 'nome' => 'Outro', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 8, 'nome' => 'Funcionário/a', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 9, 'nome' => 'Sem informação', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\CanalComercializacaoModel::insert([
            ['nome' => 'Intermediários', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Supermercados', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Mercados locais', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Ceagesp', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Grupos de consumo/Comunidades que sustentam agricultura', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Entrega em domicílio', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Restaurantes/Hotéis', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Venda no local  da produção', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Associações, ONGs', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Órgãos públicos', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Cooperativa', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Feira', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Plataformas digitais', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Outros', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\RiscoContaminacaoAguaModel::insert([
            ['nome' => 'Pulverização da Área', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Rio contaminado por propriedades vizinhas', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Água subterrânea contaminada por fossa negra', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Outros', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\TipoFonteAguaModel::insert([
            ['nome' => 'Poço caipira', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Poço artesiano/semi-artesiano', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Nascente', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Córrego/rio', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Empresa fornecedora de água (Concessionária)', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Lagoa natural', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Lagoa artificial, açude e/ou tanque', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Cisterna com captação de água da chuva', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Caminhão pipa', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\OutorgaModel::insert([
            ['nome' => 'Sim', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Não', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Não se aplica', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\DedicacaoModel::insert([
            ['nome' => 'Integral', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Parcial', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Esporádica', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\PressaoSocialModel::insert([
            ['nome' => 'Urbanização', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Usinas de cana de açúcar', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Indústria', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Setor florestal (papel e celulose)', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\CertificacaoModel::insert([
            ['nome' => 'Protocolo de Transição Agroecológica', 'form' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Orgânico por Auditoria', 'form' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Orgânico Participativo – SPG /OPAC', 'form' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Organização de Controle Social – OCS', 'form' => '', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['nome' => 'Outros', 'form' => 'descricao', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\RendaAgriculturaModel::insert([
            ['id' => 1, 'nome' => 'Sem renda agrícola', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 2, 'nome' => 'Até 25%', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 3, 'nome' => '25% até 50%', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 4, 'nome' => '50% até 75%', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 5, 'nome' => '75% até 100%', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\RendimentoComercializacaoModel::insert([
            ['id' => 1, 'nome' => 'Não comercializa', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 2, 'nome' => 'Até R$1000', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 3, 'nome' => 'De R$1000 até R$3000', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 4, 'nome' => 'De R$3000 até R$5000', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 5, 'nome' => 'Mais de R$5000', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\GrauInstrucaoModel::insert([
            ['id' => 1, 'nome' => 'Analfabeto e Fundamental 1 (completo ou incompleto)', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 2, 'nome' => 'Fundamental 2 (completo ou incompleto)', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 3, 'nome' => 'Médio (completo ou incompleto)', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 4, 'nome' => 'Superior (completo ou incompleto)', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\EsgotamentoSanitarioModel::insert([
            ['id' => 1, 'nome' => 'Fossa séptica biodigestora', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 2, 'nome' => 'Fossa negra', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 3, 'nome' => 'Esgoto canalizado', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 4, 'nome' => 'Esgotamento à céu aberto', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 5, 'nome' => 'Despejado em rios e represas', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);

        \App\Models\Core\ResiduoSolidoModel::insert([
            ['id' => 1, 'nome' => 'Possui serviço de coleta de lixo', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 2, 'nome' => 'Usa lixo orgânico na propriedade', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 3, 'nome' => 'Faz reciclagem do lixo', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 4, 'nome' => 'Faz coleta seletiva do lixo', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 5, 'nome' => 'Realiza queima do lixo', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 6, 'nome' => 'Enterra o lixo', 'created_at' => $createdAt, 'updated_at' => $createdAt],
            ['id' => 7, 'nome' => 'Descarta o lixo nos rios', 'created_at' => $createdAt, 'updated_at' => $createdAt],
        ]);
    }
}

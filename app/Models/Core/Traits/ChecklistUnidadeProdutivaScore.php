<?php

namespace App\Models\Core\Traits;

use App\Enums\ChecklistStatusEnum;
use App\Enums\CorEnum;
use App\Enums\TipoPerguntaEnum;
use App\Enums\TipoPontuacaoEnum;
use App\Models\Core\ChecklistModel;
use App\Models\Core\ChecklistPerguntaModel;
use App\Models\Core\ChecklistPerguntaRespostaModel;
use Mathepa\Expression;

/**
 * Trait criado p/ isolar a parte de "SCORE" do Formulário aplicado
 */
trait ChecklistUnidadeProdutivaScore
{
    /**
     * Retorna as categorias com os pesos máximos por categoria (Questões com cor 'verde')
     *
     * Para blocos de "Pontuação Simples", essa consulta é irrelevante, porque não precisamos saber o SCORE por COR
     *
     * @param  mixed $checklist_id
     * @param  mixed $id
     * @param  mixed $categorias
     * @param  mixed $respostasSemaforicas
     * @return array
     */
    private function getMinMaxScore($checklist_id, $id, $categorias, $respostasSemaforicas)
    {
        $resultCategorias = \DB::select('SELECT checklist_categorias.id, checklist_categorias.nome, checklist_categorias.ordem,
                    (SELECT sum(peso) FROM checklist_pergunta_respostas, respostas
                        WHERE respostas.id = checklist_pergunta_respostas.resposta_id
                            AND checklist_pergunta_respostas.deleted_at IS NULL
                            AND respostas.deleted_at IS NULL
                            AND respostas.cor = "verde"
                            AND checklist_pergunta_respostas.checklist_pergunta_id
                                IN (SELECT checklist_perguntas.id FROM checklist_perguntas, perguntas WHERE checklist_perguntas.deleted_at IS NULL AND checklist_perguntas.pergunta_id = perguntas.id AND (perguntas.tipo_pergunta != "escolha-simples-pontuacao" AND perguntas.tipo_pergunta != "escolha-simples-pontuacao-cinza") AND checklist_perguntas.checklist_categoria_id
                                    IN (SELECT id FROM checklist_categorias AS CC WHERE checklist_id = ? AND CC.id = checklist_categorias.id AND checklist_categorias.deleted_at IS NULL)))
                    AS semaforicaMaxScore,
                    (SELECT sum(peso) FROM checklist_pergunta_respostas, respostas
                        WHERE respostas.id = checklist_pergunta_respostas.resposta_id
                            AND checklist_pergunta_respostas.deleted_at IS NULL
                            AND respostas.deleted_at IS NULL
                            AND respostas.cor = "vermelho"
                            AND checklist_pergunta_respostas.checklist_pergunta_id
                                IN (SELECT checklist_perguntas.id FROM checklist_perguntas, perguntas WHERE checklist_perguntas.deleted_at IS NULL AND checklist_perguntas.pergunta_id = perguntas.id AND (perguntas.tipo_pergunta != "escolha-simples-pontuacao" AND perguntas.tipo_pergunta != "escolha-simples-pontuacao-cinza") AND checklist_perguntas.checklist_categoria_id
                                    IN (SELECT id FROM checklist_categorias AS CC WHERE checklist_id = ? AND CC.id = checklist_categorias.id AND checklist_categorias.deleted_at IS NULL)))
                    AS semaforicaMinScore
                FROM checklist_categorias, checklist_unidade_produtivas
                    WHERE checklist_categorias.checklist_id = checklist_unidade_produtivas.checklist_id
                        AND checklist_unidade_produtivas.id = ?
                        AND checklist_categorias.deleted_at IS NUll
                    ORDER BY checklist_categorias.ordem ASC;
                ', array($checklist_id, $checklist_id, $id));

        //Converte os valores (objetos) em array e torna o array dinâmico (keyBy ID)
        $resultCategorias =  collect($resultCategorias)->map(function ($x) {
            return (array) $x;
        })->keyBy('id')->all();

        //Inicio da Soma do ScoreMax - Escolha Simples com Pontuação
        $resultCategoriasEscolhaSimples = \DB::select('SELECT checklist_categorias.id, checklist_categorias.nome, checklist_categorias.ordem,
                            (SELECT sum((select peso from checklist_pergunta_respostas where checklist_pergunta_id = checklist_perguntas.id and checklist_pergunta_respostas.deleted_at is NULL order by peso desc limit 1)) as peso
                                    FROM checklist_perguntas, perguntas
                                    WHERE checklist_perguntas.deleted_at IS NULL AND checklist_perguntas.pergunta_id = perguntas.id AND (perguntas.tipo_pergunta = "escolha-simples-pontuacao" OR perguntas.tipo_pergunta = "escolha-simples-pontuacao-cinza")
                                        AND checklist_perguntas.checklist_categoria_id
                                            IN (SELECT id FROM checklist_categorias AS CC WHERE checklist_id = ? AND CC.id = checklist_categorias.id AND checklist_categorias.deleted_at IS NULL)
                            )
                    AS semaforicaMaxScore,
                            (SELECT sum((select peso from checklist_pergunta_respostas where checklist_pergunta_id = checklist_perguntas.id and checklist_pergunta_respostas.deleted_at is NULL order by peso asc limit 1)) as peso
                                            FROM checklist_perguntas, perguntas
                                            WHERE checklist_perguntas.deleted_at IS NULL AND checklist_perguntas.pergunta_id = perguntas.id AND (perguntas.tipo_pergunta = "escolha-simples-pontuacao" OR perguntas.tipo_pergunta = "escolha-simples-pontuacao-cinza")
                                                AND checklist_perguntas.checklist_categoria_id
                                                    IN (SELECT id FROM checklist_categorias AS CC WHERE checklist_id = ? AND CC.id = checklist_categorias.id AND checklist_categorias.deleted_at IS NULL)
                            )
                    AS semaforicaMinScore
                FROM checklist_categorias, checklist_unidade_produtivas
                    WHERE checklist_categorias.checklist_id = checklist_unidade_produtivas.checklist_id
                        AND checklist_unidade_produtivas.id = ?
                        AND checklist_categorias.deleted_at IS NUll
                    ORDER BY checklist_categorias.ordem ASC
                            ', array($checklist_id, $checklist_id, $id));

        $resultCategoriasEscolhaSimples =  collect($resultCategoriasEscolhaSimples)->map(function ($x) {
            return (array) $x;
        })->keyBy('id')->all();

        foreach ($resultCategorias as $k => $v) {
            if ($resultCategoriasEscolhaSimples[$k]) {
                $v['semaforicaMaxScore'] += $resultCategoriasEscolhaSimples[$k]['semaforicaMaxScore'] * 1;
                $v['semaforicaMinScore'] += $resultCategoriasEscolhaSimples[$k]['semaforicaMinScore'] * 1;
                $resultCategorias[$k] = $v;
            }
        }
        //Fim da Soma do ScoreMax - Escolha Simples com Pontuação

        //Atualiza Score Máximo caso tenha perguntas do tipo "Não se Aplica" nas questões semafóricas e pontuação simples com não se aplica
        $resultCategorias = $this->alteraScoreMaximoQuestoesNaoSeAplica($checklist_id, $resultCategorias, $categorias, $respostasSemaforicas);

        foreach ($resultCategorias as $k => $v) {
            $resultCategorias[$k]['semaforicaMaxScore'] *= 1;
            $resultCategorias[$k]['semaforicaMinScore'] *= 1;
        }

        if (ChecklistModel::withoutGlobalScopes()->findOrFail($checklist_id)->fl_nao_normalizar_percentual) {
            foreach ($resultCategorias as $k => $v) {
                $resultCategorias[$k]['semaforicaMinScore'] = 0;
            }
        }

        return $resultCategorias;
    }

    /**
     * Utilizado para subtrair os pesos máximos das respostas "NÃO SE APLICA"
     *
     * @param  mixed $checklist_id
     * @param  mixed $resultCategorias
     * @param  mixed $categorias
     * @param  mixed $respostasSemaforicas
     * @return array
     */
    private function alteraScoreMaximoQuestoesNaoSeAplica($checklist_id, $resultCategorias, $categorias, $respostasSemaforicas)
    {
        $pesosMaxPorCategoria = $this->getPesosMinMaxPorCategoria($checklist_id, 'verde');
        $pesosMinPorCategoria = $this->getPesosMinMaxPorCategoria($checklist_id, 'vermelho');

        foreach ($categorias as $k => $categoria) {
            foreach ($categoria->perguntasComPontuacao as $kk => $pergunta) {
                //&& $pergunta->tipo_pergunta !== TipoPerguntaEnum::EscolhaSimplesPontuacaoCinza
                if ($pergunta->tipo_pergunta !== TipoPerguntaEnum::NumericaPontuacao && $pergunta->tipo_pergunta !== TipoPerguntaEnum::EscolhaSimplesPontuacao) {
                    //Questões Semafóricas/Binárias/Escolha Simples Cinza
                    foreach ($pergunta->respostas as $kkk => $resposta) {

                        //Questões Não se aplica ... peso deve ser ignorado quando a resposta for essa
                        if (@$respostasSemaforicas[$resposta->id] && @$resposta->cor && $resposta->cor == CorEnum::Cinza) {
                            $pergunta_id = $respostasSemaforicas[$resposta->id]->pergunta_id;

                            $checklist_categoria_pergunta_peso = $pesosMaxPorCategoria[$pergunta_id];
                            $checklist_categoria_id = $checklist_categoria_pergunta_peso['checklist_categoria_id'];
                            $pesoDiffMax = $checklist_categoria_pergunta_peso['peso'];

                            $checklist_categoria_pergunta_peso_min = $pesosMinPorCategoria[$pergunta_id];
                            $pesoDiffMin = $checklist_categoria_pergunta_peso_min['peso'];

                            $resultCategorias[$checklist_categoria_id]['semaforicaMaxScore'] -= $pesoDiffMax;
                            $resultCategorias[$checklist_categoria_id]['semaforicaMinScore'] -= $pesoDiffMin;
                        }
                    }
                }
            }
        }

        return $resultCategorias;
    }

    /**
     * Verifica se as categorias->perguntas->respostas possuem alguma resposta do tipo "NAO SE APLICA"
     *
     * Questões semafóricas, binárias e escolha-simples-pontuacao-cinza
     *
     * @param  mixed $categorias
     * @param  mixed $respostasSemaforicas
     * @return bool
     */
    private function existRespostaNaoSeAplica($categorias, $respostasSemaforicas)
    {
        foreach ($categorias as $k => $categoria) {
            foreach ($categoria->perguntasComPontuacao as $kk => $pergunta) {
                if ($pergunta->tipo_pergunta !== TipoPerguntaEnum::NumericaPontuacao && $pergunta->tipo_pergunta !== TipoPerguntaEnum::EscolhaSimplesPontuacao) {
                    foreach ($pergunta->respostas as $kkk => $resposta) {
                        if (@$respostasSemaforicas[$resposta->id] && @$resposta->cor && $resposta->cor == CorEnum::Cinza) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Determina o sistema de pontuação (Semaforica ou Pontuacao Simples) de forma geral
     *
     * @param  mixed $categorias
     * @return string
     */
    private function tipoPontuacao($categorias)
    {
        $tipoPontuacao = 'semaforica';
        foreach ($categorias as $k => $categoria) {
            //Verifica se é Pontuação Simples
            $ret = array_filter($categoria->perguntas->toArray(), function ($pergunta) {
                return $pergunta['tipo_pergunta'] == TipoPerguntaEnum::NumericaPontuacao; // || $pergunta['tipo_pergunta'] == TipoPerguntaEnum::EscolhaSimplesPontuacao;
            });

            if (count($ret) > 0) {
                $tipoPontuacao = 'pontuacao-simples';
            }
        }

        return $tipoPontuacao;
    }

    /**
     * Determina o sistema de pontuação por categoria (Semaforica, Pontuacao Simples ou Sem pontuação)
     *
     * @param  mixed $categorias
     * @return array
     */
    private function tipoPontuacaoPorCategoria($categorias)
    {
        $tipoPontuacaoPorCategoria = array();

        foreach ($categorias as $k => $categoria) {
            $tipoPontuacao = 'sem-pontuacao';

            //Verifica se é Pontuação Simples
            $ret = array_filter($categoria->perguntas->toArray(), function ($pergunta) {
                return $pergunta['tipo_pergunta'] == TipoPerguntaEnum::NumericaPontuacao; // || $pergunta['tipo_pergunta'] == TipoPerguntaEnum::EscolhaSimplesPontuacao;
            });

            if (count($ret) > 0) {
                $tipoPontuacao = 'pontuacao-simples';
            }

            //Verifica se é Semaforica
            $ret = array_filter($categoria->perguntas->toArray(), function ($pergunta) {
                return in_array($pergunta['tipo_pergunta'], [TipoPerguntaEnum::Binaria, TipoPerguntaEnum::BinariaCinza, TipoPerguntaEnum::Semaforica, TipoPerguntaEnum::SemaforicaCinza, TipoPerguntaEnum::EscolhaSimplesPontuacao, TipoPerguntaEnum::EscolhaSimplesPontuacaoCinza]);
            });

            if (count($ret) > 0) {
                $tipoPontuacao = 'semaforica';
            }

            // $tipoPontuacaoPorCategoria[$categoria->id] = (count($ret) > 0) ? 'pontuacao-simples' : 'semaforica';
            $tipoPontuacaoPorCategoria[$categoria->id] = $tipoPontuacao;
        }

        return $tipoPontuacaoPorCategoria;
    }

    /**
     * Utilizado para subtrair os pesos máximos das respostas "NÃO SE APLICA"
     *
     * verde = "max"
     * vermelho = "min"
     *
     * @param  mixed $checklist_id
     * @param  mixed $cor
     * @return array
     */
    private function getPesosMinMaxPorCategoria($checklist_id, $cor = 'verde')
    {
        /*
            Vai retornar uma lista de todos os pesos das perguntas que foram utilizadas no formulário com o peso MAX ou MIN.
            No momento que for feito o "collect()->map" ele vai fixar o "pergunta_id" com o peso/
            O "ORDER BY peso" defini se vai pegar os pesos "máximos" das perguntas ou os pesos "minimos".
            PS: Esta retornando TODOS os pesos da pergunta, mas apenas UM é fixado no "pergunta_id" (por isso o sorting)
        */
        $pesosPorCategoria = \DB::select(
            'SELECT * FROM checklist_perguntas, checklist_pergunta_respostas, respostas
                                    WHERE checklist_perguntas.id = checklist_pergunta_respostas.checklist_pergunta_id
                                        AND respostas.id = checklist_pergunta_respostas.resposta_id
                                        AND checklist_perguntas.deleted_at IS NULL
                                        AND checklist_pergunta_respostas.deleted_at IS NULL
                                        AND respostas.deleted_at IS NULL
                                        AND checklist_pergunta_respostas.checklist_pergunta_id
                                            IN (SELECT checklist_perguntas.id FROM checklist_perguntas WHERE checklist_perguntas.deleted_at IS NULL AND checklist_perguntas.checklist_categoria_id
                                                IN (SELECT id FROM checklist_categorias AS CC WHERE checklist_id = ? AND CC.deleted_at IS NULL))
            ORDER BY peso ' . ($cor == 'verde' ? 'ASC' : 'DESC'),
            array($checklist_id)
        );

        $pesosPorCategoria =  collect($pesosPorCategoria)->map(function ($x) {
            return (array) $x;
        })->keyBy('pergunta_id')->all();

        return $pesosPorCategoria;
    }

    /**
     * getRespostasParaCalculoPontuacao
     *
     * @param  mixed $checklistUnidadeProdutiva
     * @return mixed
     */
    private function getRespostasParaCalculoPontuacao($checklistUnidadeProdutiva)
    {
        $with = ['pergunta' => function ($query) {
            //Semafórica/Binárias
            $query->where('tipo_pergunta', 'semaforica')
                ->orWhere('tipo_pergunta', 'semaforica-cinza')
                ->orWhere('tipo_pergunta', 'binaria')
                ->orWhere('tipo_pergunta', 'binaria-cinza')

                //Numérica pontuação
                ->orWhere('tipo_pergunta', 'numerica-pontuacao')

                //Escolha Simples (É considerada uma semafórica)
                ->orWhere('tipo_pergunta', 'escolha-simples-pontuacao')
                ->orWhere('tipo_pergunta', 'escolha-simples-pontuacao-cinza');
        }, 'respostas'];

        //Dependendo o status do checklist as respostas estão em locais diferentes
        if ($checklistUnidadeProdutiva->status == ChecklistStatusEnum::Rascunho || $checklistUnidadeProdutiva->status == ChecklistStatusEnum::AguardandoAprovacao) {
            //Respostas na Unidade Produtiva
            $respostas = $checklistUnidadeProdutiva->respostasUnidadeProdutivaMany()->with($with)->get();
        } else {
            //Respostas de Checklists Finalizados
            $respostas = $checklistUnidadeProdutiva->respostasMany()->with($with)->get();
        }

        return $respostas;
    }

    /**
     * Resultado da fórmula personalizada (caso tenha)
     *
     * @param  mixed $checklist
     * @param  mixed $resultCategorias
     * @return array
     */
    private function getResultFormula($checklist, $resultCategorias)
    {
        $result = null;

        if ($checklist->tipo_pontuacao == TipoPontuacaoEnum::ComPontuacaoFormulaPersonalizada && $checklist->formula) {
            $result = array('resultado' => null, 'formula' => null, 'plain' => null);

            $resultado = null;
            $plain = $this->checklist->formula;

            try {
                $parser = new Expression($this->checklist->formula);
                foreach ($resultCategorias as $k => $categoria) {
                    $value = @$categoria['somaSimplesScore'] ? $categoria['somaSimplesScore'] : 0;
                    $parser->setVariable('C' . $categoria['id'], $value);
                    $plain = str_replace('C' . $categoria['id'], $value, $plain);
                }

                $resultado = $checklist->formula_prefix . ' ' . number_format($parser->evaluate(), 2) . ' ' . $checklist->formula_sufix;
            } catch (\Exception $e) {
                $resultado = 'Fórmula inválida';
            }

            $result['formula'] = $checklist->formula;
            $result['resultado'] = $resultado;
            $result['plain'] = $plain;
        }

        return $result;
    }

    /**
     * Retorna os pesos das respostas [1=>10, 2=>20]
     *
     * @param  mixed $categorias
     * @return array
     */
    private function getPesosRespostas($categorias)
    {
        $checklist_pergunta_ids = ChecklistPerguntaModel::whereIn('checklist_categoria_id', $categorias->pluck('id')->toArray())->pluck('id')->toArray();
        return ChecklistPerguntaRespostaModel::whereIn('checklist_pergunta_id', $checklist_pergunta_ids)->pluck("peso", "resposta_id");
    }

    /**
     * Este bloco abaixo não vale apena converter em SQL porque tem muitas variações
     *     - Respostas podem estar na Unidade Produtiva ou na Tabela de Checklist Finalizados
     *     - Precisaria separar para os 4 tipos de cores ... e esse mesmo bloco não poderia ser reaproveitado para perguntas do tipo pontuação
     *     - Teria que ser feito um transpose dos resultados agrupando por categoria, para depois interligar com os dados de score Máximo
     *     - Existe uma mescla de calculo entre semafóricas e numerica com pontuação (multiplicação de pesos)
     */
    public function score()
    {
        if ($this->checklist->tipo_pontuacao == TipoPontuacaoEnum::SemPontuacao) {
            return null;
        }

        //Categorias do checklist (com as perguntas e respostas)
        $categorias = $this->checklist->categorias()->with('perguntasComPontuacao.respostas')->get();

        //Tipo da pontuação
        $tipoPontuacao = $this->tipoPontuacao($categorias);

        $tipoPontuacaoPorCategoria = $this->tipoPontuacaoPorCategoria($categorias);

        //Array dinamico das respostas com os pesos (Será utilizado para extrair os pesos em respostas Semafóricas ou Escolha-Simples)
        $pesosRespostas = $this->getPesosRespostas($categorias);

        //Cores em array
        $cores = array_fill_keys(CorEnum::getValues(), 0);

        //Respostas apenas das perguntas que possuem pontuação
        $respostas = $this->getRespostasParaCalculoPontuacao($this);

        //Arrays dinâmicos das respostas do usuário
        $respostasSemaforicas = $respostas->keyBy('resposta_id');
        $respostasPontuacaoSimples = $respostas->keyBy('pergunta_id');

        $resultCategorias = $this->getMinMaxScore($this->checklist_id, $this->id, $categorias, $respostasSemaforicas);

        $existeRespostaNaoSeAplica = $this->existRespostaNaoSeAplica($categorias, $respostasSemaforicas);

        foreach ($categorias as $k => $categoria) {
            $coresScore = array_merge([], $cores);
            $coresRespostas = array_merge([], $cores);

            $semaforicaScore = 0;

            $somaSimplesScore = 0;
            $somaSimplesRespostas = 0;

            $coresScore['numerica'] = 0;
            $coresRespostas['numerica'] = 0;

            //Varre todas as perguntas / respostas das categorias e verifica se foi respondido ou não
            foreach ($categoria->perguntasComPontuacao as $kk => $pergunta) {
                //Tipo Numérica Com Pontuação (Pega o valor informado pelo usuário e multiplica pelo Peso da Pergunta (atrelado ao checklist especifico))
                if ($pergunta->tipo_pergunta == TipoPerguntaEnum::NumericaPontuacao) {
                    if (@$respostasPontuacaoSimples[$pergunta->id]) { //Tem Resposta
                        $resposta = $respostasPontuacaoSimples[$pergunta->id];
                        $pesoPergunta = $pergunta->pivot->peso_pergunta;

                        $total = ($pesoPergunta * @$resposta['resposta'] * 1);
                        //Score na "Pontuação Simples"
                        $somaSimplesScore += $total;
                        $somaSimplesRespostas += 1;

                        $coresScore['numerica'] += $total;
                        $coresRespostas['numerica'] += 1;
                    }
                } else if ($pergunta->tipo_pergunta == TipoPerguntaEnum::EscolhaSimplesPontuacao) { //Esse tipo de questão é considerada uma Semafórica (calculo percentual)
                    //Tipo Escolha Simples (varre respostas e extrai o peso da resposta)
                    foreach ($pergunta->respostas as $resposta) {
                        if (@$respostasSemaforicas[$resposta->id]) {
                            //Score na "Pontuação Simples"
                            $somaSimplesScore += @$pesosRespostas[$resposta->id];
                            $somaSimplesRespostas += 1;

                            //Score na Semafórica
                            $semaforicaScore += @$pesosRespostas[$resposta->id];

                            $coresScore['numerica'] +=  @$pesosRespostas[$resposta->id];
                            $coresRespostas['numerica'] += 1;
                        }
                    }
                } else if ($pergunta->tipo_pergunta == TipoPerguntaEnum::EscolhaSimplesPontuacaoCinza) { //Esse tipo de questão é considerada uma Semafórica (calculo percentual)
                    //Tipo Escolha Simples (varre respostas e extrai o peso da resposta)
                    foreach ($pergunta->respostas as $resposta) {
                        //Escolha Simples
                        if (@$respostasSemaforicas[$resposta->id] && !@$resposta->cor) {
                            //Score na "Pontuação Simples"
                            $somaSimplesScore += @$pesosRespostas[$resposta->id];
                            $somaSimplesRespostas += 1;

                            //Score na Semafórica
                            $semaforicaScore += @$pesosRespostas[$resposta->id];

                            $coresScore['numerica'] +=  @$pesosRespostas[$resposta->id];
                            $coresRespostas['numerica'] += 1;
                        } else if (@$respostasSemaforicas[$resposta->id] && @$resposta->cor) {
                            //Escolha Semafórica
                            //Cinza/Não se Aplica não tem pontuação e deve ser ignorada no calculo (maxScore/minScore)
                            $coresScore[$resposta->cor] += @$pesosRespostas[$resposta->id];
                            $coresRespostas[$resposta->cor] += 1;

                            //Total questões semafóricas
                            $semaforicaScore += @$pesosRespostas[$resposta->id];

                            //Perguntas semafóricas também somam na "Pontuação Simples"
                            $somaSimplesScore += @$pesosRespostas[$resposta->id];
                            $somaSimplesRespostas += 1;
                        }
                    }
                } else {
                    //Perguntas que contem respostas Semafóricas
                    foreach ($pergunta->respostas as $resposta) {
                        if (@$respostasSemaforicas[$resposta->id] && @$resposta->cor) {
                            //Cinza/Não se Aplica não tem pontuação e deve ser ignorada no calculo (maxScore/minScore)
                            $coresScore[$resposta->cor] += @$pesosRespostas[$resposta->id];
                            $coresRespostas[$resposta->cor] += 1;

                            //Total questões semafóricas
                            $semaforicaScore += @$pesosRespostas[$resposta->id];

                            //Perguntas semafóricas também somam na "Pontuação Simples"
                            $somaSimplesScore += @$pesosRespostas[$resposta->id];
                            $somaSimplesRespostas += 1;
                        }
                    }
                }

                //Tipo pontuação = Semafórica
                $resultCategorias[$categoria->id]['coresScore'] = $coresScore;
                $resultCategorias[$categoria->id]['coresRespostas'] = $coresRespostas;

                $semaforicaMaxScore = $resultCategorias[$categoria->id]['semaforicaMaxScore'];
                $semaforicaMinScore = $resultCategorias[$categoria->id]['semaforicaMinScore'];

                $resultCategorias[$categoria->id]['semaforicaScore'] = $semaforicaScore;
                $resultCategorias[$categoria->id]['semaforicaNota10'] = $semaforicaMaxScore > 0 ? @round((($semaforicaScore - $semaforicaMinScore) / ($semaforicaMaxScore - $semaforicaMinScore)) * 10, 2) : 0;

                //Tipo pontuação = Pontuação Simples
                $resultCategorias[$categoria->id]['somaSimplesScore'] = $somaSimplesScore;
                $resultCategorias[$categoria->id]['somaSimplesRespostas'] = $somaSimplesRespostas;

                //Resultado final, normalizado p/ o front consumir
                if ($tipoPontuacaoPorCategoria[$categoria->id] == 'pontuacao-simples') {
                    $resultCategorias[$categoria->id]['pontuacao'] = $somaSimplesScore;
                    $resultCategorias[$categoria->id]['pontuacaoPercentual'] = '-';
                    $resultCategorias[$categoria->id]['pontuacaoMobile'] = $somaSimplesScore . ' pontos';
                } else if ($tipoPontuacaoPorCategoria[$categoria->id] == 'semaforica') {
                    $resultCategorias[$categoria->id]['pontuacao'] =  $somaSimplesScore; //Cliente solicitou alterar regra = é a soma da pontuação final e não de 1-10 // $resultCategorias[$categoria->id]['semaforicaNota10'];
                    $resultCategorias[$categoria->id]['pontuacaoPercentual'] = ($resultCategorias[$categoria->id]['semaforicaNota10'] * 10) . '%';
                    $resultCategorias[$categoria->id]['pontuacaoMobile'] = $resultCategorias[$categoria->id]['pontuacaoPercentual'];
                } else {
                    $resultCategorias[$categoria->id]['pontuacao'] = 0;
                    $resultCategorias[$categoria->id]['pontuacaoPercentual'] = 0;
                    $resultCategorias[$categoria->id]['pontuacaoMobile'] = 0;
                }
            }
        }

        //Percentual final
        $semaforicaMax = 0;
        $semaforicaMin = 0;
        $semaforicaScore = 0;
        $somaSimplesScore = 0;
        $coresRespostas = array_merge([], $cores);
        $coresRespostas['numerica'] = 0;

        foreach ($resultCategorias as $k => $categoria) {
            //Semafórica
            $semaforicaMax += @$categoria['semaforicaMaxScore'];
            $semaforicaMin += @$categoria['semaforicaMinScore'];
            $semaforicaScore += @$categoria['semaforicaScore'];

            if (@$categoria['coresRespostas']) {
                foreach ($categoria['coresRespostas'] as $kk => $vv) {
                    $coresRespostas[$kk] += $vv;
                }
            }

            //Soma simples
            $somaSimplesScore += @$categoria['somaSimplesScore'];
        }


        //Semaforica
        // $semaforicaNota10 = $semaforicaMax > 0 || $semaforicaMin < 0 ? @round((($semaforicaScore - $semaforicaMin) / ($semaforicaMax - $semaforicaMin)) * 10, 2) : 0;
        $semaforicaNota10 = $semaforicaMax > 0 || $semaforicaMin < 0 ? @round((($semaforicaScore - $semaforicaMin) / ($semaforicaMax - $semaforicaMin)) * 10, 3) : 0;


        //Fórmula Personalizada
        $formula = $this->getResultFormula($this->checklist, $resultCategorias);


        //Final do processo, se só foi respondidas questões "NÃO SE APLICA", deve retornar um traço (-) no percentual
        foreach ($resultCategorias as $k => $v) {
            if (@$tipoPontuacaoPorCategoria[$v['id']] == 'semaforica') {
                if ($v['coresRespostas']['verde'] == 0 && $v['coresRespostas']['amarelo'] == 0 && $v['coresRespostas']['vermelho'] == 0 && $v['coresRespostas']['numerica'] == 0) {
                    $v['pontuacaoPercentual'] = '-';
                    $v['pontuacaoMobile'] = '-';
                    $resultCategorias[$k] = $v;
                }
            }
        }

        //Retorna apenas o que é importante no "resultCategorias" (o resto foi utilizado p/ calculos, mas o front não precisa receber os valores)
        $resultCategoriasNovo = array();
        foreach ($resultCategorias as $k => $v) {
            $resultCategoriasNovo[$k] = collect($v)->only(['id', 'nome', 'ordem', 'coresScore', 'coresRespostas', 'pontuacao', 'pontuacaoPercentual', 'pontuacaoMobile'])->toArray();
        }

        $pontuacaoFinal =  $tipoPontuacao == 'pontuacao-simples' ? $somaSimplesScore : ($semaforicaNota10 * 10) . '%';

        if ($formula) {
            $pontuacaoFinal = $formula['resultado'];
        }


        $pontuacaoPercentual = $tipoPontuacao == 'pontuacao-simples' ? '-' : ($semaforicaNota10 * 10) . '%';


        //Final do processo, se só foi respondidas questões "NÃO SE APLICA" e for do tipo "SEMAFORICA" ... deve retornar um traço (-) no percentual
        if ($tipoPontuacao == 'semaforica' && $coresRespostas['verde'] == 0 && $coresRespostas['amarelo'] == 0 && $coresRespostas['vermelho'] == 0 && $coresRespostas['numerica'] == 0) {
            $pontuacaoPercentual = '-';
        }

        //Bloco de debug
        $coresScore = array_merge([], $cores);
        foreach ($resultCategoriasNovo as $k => $v) {
            if (@$v['coresScore']) {
                foreach ($v['coresScore'] as $kk => $vv) {
                    @$coresScore[$kk] += $vv;
                }
            }
        }

        //Retorno dos dados
        $return = array(
            'tipoPontuacao' => $tipoPontuacao,

            'formula' => $formula,

            'coresRespostas' => $coresRespostas,

            //Debug
            'coresScore' => $coresScore,
            'semaforicaMax' => $semaforicaMax,
            'semaforicaMin' => $semaforicaMin,

            'categorias' => $resultCategoriasNovo,

            'pontuacaoFinal' => $pontuacaoFinal,

            'pontuacao' =>  $somaSimplesScore, //Pontuação é o score total (Não o nota10) // $tipoPontuacao == 'pontuacao-simples' ? $somaSimplesScore : $semaforicaNota10,

            'pontuacaoPercentual' => $pontuacaoPercentual,

            'possuiColunaNaoSeAplica' => $existeRespostaNaoSeAplica,
        );

        // dd($return, $resultCategorias);

        return $return;
    }
}

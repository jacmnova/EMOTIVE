<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; 

class Formulario extends Model
{
    use HasFactory;

    protected $table = 'formularios';

    protected $fillable = [
        'nome',
        'label',
        'descricao',
        'instrucoes',
        'score_ini',
        'score_fim',
        'calculo_id',
        'status',
    ];

    protected $casts = [
        'score_ini' => 'integer',
        'score_fim' => 'integer',
        'calculo_id' => 'integer',
        'status' => 'boolean',
    ];

    public function tipoCalculo()
    {
        return $this->belongsTo(TipoCalculo::class, 'calculo_id');
    }

    public function perguntaCount()
    {
        return $this->hasMany(Pergunta::class, 'formulario_id')->count();
    }

    public function variaveisCount()
    {
        return $this->hasMany(Variavel::class, 'formulario_id')->count();
    }

    public function perguntasPorVariavel()
    {
        return DB::table('perguntas as p')
            ->leftJoin('pergunta_variavel as pv', 'pv.pergunta_id', '=', 'p.id')
            ->leftJoin('variaveis as v', 'v.id', '=', 'pv.variavel_id')
            ->select('v.nome', DB::raw('COUNT(DISTINCT p.id) as total_perguntas'))
            ->where('p.formulario_id', $this->id)
            ->groupBy('v.nome')
            ->orderBy('v.nome')
            ->get();
    }

    public function perguntasComVariaveis()
    {
        return DB::table('perguntas as p')
            ->leftJoin('pergunta_variavel as pv', 'pv.pergunta_id', '=', 'p.id')
            ->leftJoin('variaveis as v', 'v.id', '=', 'pv.variavel_id')
            ->select('p.numero_da_pergunta', 'p.pergunta', 'v.nome', 'v.tag')
            ->where('p.formulario_id', $this->id)
            ->orderBy('p.id')
            ->get();
    }

    public function variaveisDetalhadas()
    {
        // Obtém o score_fim uma única vez
        $scoreFim = $this->score_fim;

        // Subconsulta para obter o total de perguntas por variável
        $perguntasSubQuery = DB::table('nr1.perguntas as p')
            ->select('v.tag', DB::raw('COUNT(DISTINCT p.id) as total_perguntas'))
            ->leftJoin('nr1.pergunta_variavel as pv', 'pv.pergunta_id', '=', 'p.id')
            ->leftJoin('nr1.variaveis as v', 'v.id', '=', 'pv.variavel_id')
            ->where('p.formulario_id', $this->id)
            ->groupBy('v.tag');

        // Executa a consulta principal
        return DB::table('nr1.variaveis as var')
            ->select(
                'var.nome',
                'var.B as baixa',
                'var.M as media',
                'var.A as alta',
                // Join com a subconsulta de perguntas
                DB::raw('IFNULL(pq.total_perguntas, 0) as pergunta'),
                // Cálculo do max
                DB::raw('IFNULL(pq.total_perguntas, 0) * ' . $scoreFim . ' as max')
            )
            ->leftJoin(
                DB::raw("({$perguntasSubQuery->toSql()}) as pq"),
                'pq.tag', '=', 'var.tag'
            )
            ->mergeBindings($perguntasSubQuery) // para passar bindings da subquery
            ->where('var.formulario_id', $this->id)
            ->get();
    }

    public function perguntas()
    {
        return $this->hasMany(Pergunta::class, 'formulario_id', 'id');
    }

    public function etapas()
    {
        return $this->hasMany(FormularioEtapa::class, 'formulario_id');
    }
}
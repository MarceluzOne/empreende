<?php

namespace App\Http\Controllers;

use App\Services\CpfBackfillService;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Execução do backfill de users.cpf pelo backoffice.
 *
 * Existe porque o servidor de produção não tem SSH: sem uma tela, não há como
 * rodar a migration 2026_08_06_000004, e sem ela o índice único de users.cpf
 * não protege ninguém — toda conta anterior à coluna fica com o campo nulo.
 *
 * ROTA TEMPORÁRIA — remover de routes/sites/empreendevitoria.php junto com
 * este controller e a view assim que o backfill estiver rodado em produção,
 * no mesmo espírito da /setup-inicial.
 */
class CpfBackfillController extends Controller
{
    private CpfBackfillService $backfill;

    public function __construct(CpfBackfillService $backfill)
    {
        $this->backfill = $backfill;
    }

    public function index()
    {
        $ready = Schema::hasColumn('users', 'cpf');

        return view('cpf-backfill', [
            'ready'   => $ready,
            'preview' => $ready ? $this->backfill->preview() : null,
        ]);
    }

    /**
     * Grava de fato. A exceção é mostrada na tela porque, sem SSH, ler o log
     * de produção não é prático.
     */
    public function run()
    {
        if (! Schema::hasColumn('users', 'cpf')) {
            return back()->with('erro', 'A coluna users.cpf ainda não existe no banco. Rode o ALTER TABLE antes.');
        }

        try {
            $result = $this->backfill->run();
        } catch (Throwable $e) {
            return back()->with('erro', get_class($e).': '.$e->getMessage());
        }

        return back()->with('ok', $this->summarize($result));
    }

    /**
     * @param array<string, int> $result
     */
    private function summarize(array $result): string
    {
        if ($result['total'] === 0) {
            return 'Nenhuma conta precisou de preenchimento. Restam '.$result['restantes'].' sem CPF.';
        }

        return $result['total'].' conta(s) preenchida(s): '
            .$result['curriculos'].' pelo currículo e '.$result['eventos'].' por inscrição em evento. '
            .'Restam '.$result['restantes'].' sem CPF.';
    }
}

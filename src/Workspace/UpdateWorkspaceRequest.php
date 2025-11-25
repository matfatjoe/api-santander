<?php

namespace Matfatjoe\SantanderBoleto\Workspace;

use Matfatjoe\SantanderBoleto\Models\Covenant;

/**
 * Requisição para atualizar um workspace existente
 */
class UpdateWorkspaceRequest
{
    private $covenants;
    private $description;

    /**
     * @param Covenant[]|null $covenants Lista de convênios
     * @param string|null $description Descrição do workspace
     */
    public function __construct(?array $covenants = null, ?string $description = null)
    {
        $this->covenants = $covenants;
        $this->description = $description;
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->covenants !== null) {
            $data['covenants'] = array_map(fn($c) => $c->toArray(), $this->covenants);
        }

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        return $data;
    }
}

<?php

namespace App\Traits;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

trait Auditable
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $event) =>
                $this->buildActivityDescription($event)
            );
    }

    protected function buildActivityDescription(string $event): string
    {
        $model = class_basename($this);

        $label = config('audit.models.' . $model, $model);

        $nome = $this->getAuditName();

        return match ($event) {
            'created' => "Criou {$label} {$nome}",
            'updated' => "Atualizou {$label} {$nome}",
            'deleted' => "Excluiu {$label} {$nome}",
            default   => ucfirst($event) . " {$label} {$nome}",
        };
    }

    protected function getAuditName(): string
    {
        return $this->name
            ?? $this->titulo
            ?? $this->descricao
            ?? "#{$this->id}";
    }
}

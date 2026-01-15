<?php

namespace JobMetric\Setting\Events;

use JobMetric\EventSystem\Contracts\DomainEvent;
use JobMetric\EventSystem\Support\DomainEventDefinition;

readonly class StoreSettingEvent implements DomainEvent
{
    /**
     * Create a new event instance.
     */
    public function __construct(
        public string $form
    ) {
    }

    /**
     * Returns the stable technical key for the domain event.
     *
     * @return string
     */
    public static function key(): string
    {
        return 'setting.store';
    }

    /**
     * Returns the full metadata definition for this domain event.
     *
     * @return DomainEventDefinition
     */
    public static function definition(): DomainEventDefinition
    {
        return new DomainEventDefinition(self::key(), 'setting::base.entity_names.setting', 'setting::base.events.setting_store.title', 'setting::base.events.setting_store.description', 'fas fa-save', [
            'setting',
            'storage',
            'management',
        ]);
    }
}

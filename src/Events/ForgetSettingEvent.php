<?php

namespace JobMetric\Setting\Events;

use JobMetric\EventSystem\Contracts\DomainEvent;
use JobMetric\EventSystem\Support\DomainEventDefinition;

readonly class ForgetSettingEvent implements DomainEvent
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
        return 'setting.forget';
    }

    /**
     * Returns the full metadata definition for this domain event.
     *
     * @return DomainEventDefinition
     */
    public static function definition(): DomainEventDefinition
    {
        return new DomainEventDefinition(self::key(), 'setting::base.entity_names.setting', 'setting::base.events.setting_forget.title', 'setting::base.events.setting_forget.description', 'fas fa-trash', [
            'setting',
            'deletion',
            'management',
        ]);
    }
}

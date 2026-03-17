<?php

namespace JobMetric\Setting\Support;

use JobMetric\Setting\Contracts\AbstractSetting;
use ReflectionClass;
use Throwable;

/**
 * Registry for AbstractSetting class FQCNs. Only stores classes that live in a namespace from
 * SettingNamespaceRegistry and extend AbstractSetting.
 *
 * @package JobMetric\Setting
 *
 * @property-read array<int, string> $settings Registered setting class FQCNs (internal state)
 */
class SettingRegistry
{
    /**
     * The settings that have been registered.
     *
     * @var array<int, string> Setting class FQCNs.
     */
    protected array $settings = [];

    public function __construct(
        protected SettingNamespaceRegistry $namespaceRegistry
    ) {
    }

    /**
     * Whether the class is allowed: extends AbstractSetting and its namespace is in SettingNamespaceRegistry.
     *
     * @param string $class Class FQCN.
     *
     * @return bool
     */
    protected function allowed(string $class): bool
    {
        $class = trim($class, '\\');
        if ($class === '' || ! class_exists($class)) {
            return false;
        }

        if (! is_subclass_of($class, AbstractSetting::class)) {
            return false;
        }

        $namespace = (new ReflectionClass($class))->getNamespaceName();

        return $this->namespaceRegistry->has($namespace);
    }

    /**
     * Register a setting form class. Only added if it extends AbstractSetting and its namespace is in
     * SettingNamespaceRegistry.
     *
     * @param string $class AbstractSetting class FQCN (e.g. App\Settings\ConfigSetting).
     *
     * @return self
     */
    public function register(string $class): self
    {
        $class = trim($class, '\\');
        if ($class !== '' && $this->allowed($class) && ! in_array($class, $this->settings, true)) {
            $this->settings[] = $class;
        }

        return $this;
    }

    /**
     * Remove a setting class from the registry.
     *
     * @param string $class Setting class FQCN to remove.
     *
     * @return self
     */
    public function unregister(string $class): self
    {
        $class = trim($class, '\\');
        $key = array_search($class, $this->settings, true);
        if ($key !== false) {
            unset($this->settings[$key]);
            $this->settings = array_values($this->settings);
        }

        return $this;
    }

    /**
     * Check whether a setting class is registered.
     *
     * @param string $class Setting class FQCN to check.
     *
     * @return bool
     */
    public function has(string $class): bool
    {
        return in_array(trim($class, '\\'), $this->settings, true);
    }

    /**
     * Return all registered setting class FQCNs.
     *
     * @return array<int, string>
     */
    public function all(): array
    {
        return $this->settings;
    }

    /**
     * Resolve the full spec for a setting class by instantiating and calling toArray().
     *
     * @param string $class Setting class FQCN.
     *
     * @return array<string, mixed>|null Spec array (application, key, title, description, form_name, form), or null if
     *                       not loadable.
     * @throws Throwable
     */
    public function resolveSpec(string $class): ?array
    {
        if (! class_exists($class)) {
            return null;
        }

        $instance = app()->make($class);
        if (! $instance instanceof AbstractSetting) {
            return null;
        }

        return $instance->toArray();
    }

    /**
     * Clear all registered setting classes.
     *
     * @return self
     */
    public function clear(): self
    {
        $this->settings = [];

        return $this;
    }

    /**
     * Discover and register all AbstractSetting classes in every namespace from SettingNamespaceRegistry.
     *
     * @return self
     */
    public function discover(): self
    {
        foreach ($this->namespaceRegistry->all() as $namespace) {
            $namespace = trim($namespace, '\\');
            if ($namespace === '') {
                continue;
            }

            $path = resolveNamespacePath($namespace);
            if ($path === null || ! is_dir($path)) {
                continue;
            }

            $files = glob($path . DIRECTORY_SEPARATOR . '*.php') ?: [];
            foreach ($files as $file) {
                $shortName = basename($file, '.php');
                $fqcn = $namespace . '\\' . $shortName;
                $this->register($fqcn);
            }
        }

        return $this;
    }
}

<?php

namespace JobMetric\Setting\Support;

/**
 * Registry for setting namespaces. Allows adding namespaces from multiple sources in one array.
 *
 * @package JobMetric\Setting
 *
 * @property-read array<int, string> $namespaces List of registered namespaces (internal state)
 */
class SettingNamespaceRegistry
{
    /**
     * Registered namespaces.
     *
     * @var array<int, string>
     */
    protected array $namespaces = [];

    /**
     * Register a namespace.
     *
     * @param string $namespace Namespace to add (e.g. App\Settings, Vendor\Package\Settings).
     *
     * @return self
     */
    public function register(string $namespace): self
    {
        $namespace = trim($namespace, '\\');
        if ($namespace !== '' && ! in_array($namespace, $this->namespaces, true)) {
            $this->namespaces[] = $namespace;
        }

        return $this;
    }

    /**
     * Remove a namespace from the registry.
     *
     * @param string $namespace Namespace to remove.
     *
     * @return self
     */
    public function unregister(string $namespace): self
    {
        $namespace = trim($namespace, '\\');
        $key = array_search($namespace, $this->namespaces, true);
        if ($key !== false) {
            unset($this->namespaces[$key]);
            $this->namespaces = array_values($this->namespaces);
        }

        return $this;
    }

    /**
     * Check whether a namespace is registered.
     *
     * @param string $namespace Namespace to check.
     *
     * @return bool
     */
    public function has(string $namespace): bool
    {
        return in_array(trim($namespace, '\\'), $this->namespaces, true);
    }

    /**
     * Get all registered namespaces.
     *
     * @return array<int, string>
     */
    public function all(): array
    {
        return $this->namespaces;
    }

    /**
     * Remove all registered namespaces.
     *
     * @return self
     */
    public function clear(): self
    {
        $this->namespaces = [];

        return $this;
    }
}

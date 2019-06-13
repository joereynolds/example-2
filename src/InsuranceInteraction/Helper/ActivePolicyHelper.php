<?php

namespace InsuranceInteraction\Helper;

interface ActivePolicyHelper
{
    /**
     * Is an active policy available
     * @return bool
     */
    public function hasPolicy(): bool;

    /**
     * Return the currently active policy
     * @return Policy
     */
    public function getPolicy();

    /**
     * Manually set a policy on the helper
     * @param Policy $policy
     * @return self
     */
    public function setPolicy(Policy $policy): ActivePolicyHelper;

    /**
     * Attempt to load a policy into the helper via an identifier
     * @param string $identifier
     * @return bool True on success, false if no quotation loaded
     */
    public function loadPolicy(string $identifier): bool;

    /**
     * Save the active policy back to the persistence layer
     * @return bool True on success, false if save failed
     */
    public function savePolicy(): bool;
}

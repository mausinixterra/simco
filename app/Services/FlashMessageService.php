<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Service responsible for managing flash messages in session
 */
class FlashMessageService
{
    private const FLASH_KEY = '_flash_messages';
    
    /**
     * Stores a flash message in the session
     * 
     * @param string $key Message key/identifier
     * @param mixed $value Message value
     */
    public function set(string $key, $value): void
    {
        if (!isset($_SESSION[self::FLASH_KEY])) {
            $_SESSION[self::FLASH_KEY] = [];
        }
        
        $_SESSION[self::FLASH_KEY][$key] = $value;
    }
    
    /**
     * Retrieves and removes a flash message from the session
     * 
     * @param string $key Message key/identifier
     * @return mixed Message value or null if not found
     */
    public function get(string $key)
    {
        if (!isset($_SESSION[self::FLASH_KEY][$key])) {
            return null;
        }
        
        $value = $_SESSION[self::FLASH_KEY][$key];
        unset($_SESSION[self::FLASH_KEY][$key]);
        
        // Clean up if no more flash messages
        if (empty($_SESSION[self::FLASH_KEY])) {
            unset($_SESSION[self::FLASH_KEY]);
        }
        
        return $value;
    }
    
    /**
     * Checks if a flash message exists without removing it
     * 
     * @param string $key Message key/identifier
     * @return bool True if message exists
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[self::FLASH_KEY][$key]);
    }
    
    /**
     * Removes a specific flash message
     * 
     * @param string $key Message key/identifier
     */
    public function remove(string $key): void
    {
        if (isset($_SESSION[self::FLASH_KEY][$key])) {
            unset($_SESSION[self::FLASH_KEY][$key]);
            
            // Clean up if no more flash messages
            if (empty($_SESSION[self::FLASH_KEY])) {
                unset($_SESSION[self::FLASH_KEY]);
            }
        }
    }
    
    /**
     * Clears all flash messages
     */
    public function clear(): void
    {
        if (isset($_SESSION[self::FLASH_KEY])) {
            unset($_SESSION[self::FLASH_KEY]);
        }
    }
    
    /**
     * Sets a success message
     * 
     * @param array $data Success data
     */
    public function success(array $data): void
    {
        $this->set('pqrsf_success', $data);
    }
    
    /**
     * Sets an error message
     * 
     * @param array $data Error data
     */
    public function error(array $data): void
    {
        $this->set('form_status', $data);
    }
    
    /**
     * Gets success message
     * 
     * @return array|null Success data or null
     */
    public function getSuccess(): ?array
    {
        $value = $this->get('pqrsf_success');
        return is_array($value) ? $value : null;
    }
    
    /**
     * Gets error message
     * 
     * @return array|null Error data or null
     */
    public function getError(): ?array
    {
        $value = $this->get('form_status');
        return is_array($value) ? $value : null;
    }
}

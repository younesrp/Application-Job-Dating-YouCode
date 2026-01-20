<?php
namespace App\app\core;
use App\app\core\Database;
use App\app\core\Security;

class Validator
{
    private $errors = [];
    private $data = [];
    private $security;

    public function __construct()
    {
        $this->security = new Security();
    }

    /**
     * Valide les données selon les règles
     */
    public function validate(array $data, array $rules): bool
    {
        $this->data = $data;
        $this->errors = [];

        foreach ($rules as $field => $ruleString) {
            $rulesArray = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            foreach ($rulesArray as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return empty($this->errors);
    }

    /**
     * Applique une règle de validation
     */
    private function applyRule(string $field, $value, string $rule): void
    {
        // Parse rule avec paramètres (ex: min:5)
        $parts = explode(':', $rule);
        $ruleName = $parts[0];
        $parameter = $parts[1] ?? null;

        switch ($ruleName) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->addError($field, "Le champ {$field} est requis");
                }
                break;

            case 'email':
                if (!$this->security->validateEmail($value)) {
                    $this->addError($field, "Le champ {$field} doit être un email valide");
                }
                break;

            case 'password':
                if (!$this->security->validatePassword($value)) {
                    $this->addError($field, $this->security->getPasswordValidationError($value));
                }
                break;

            case 'url':
                if (!$this->security->validateUrl($value)) {
                    $this->addError($field, "Le champ {$field} doit être une URL valide");
                }
                break;

            case 'ip':
                if (!$this->security->validateIP($value)) {
                    $this->addError($field, "Le champ {$field} doit être une adresse IP valide");
                }
                break;

            case 'integer':
                if (!$this->security->validateInteger($value)) {
                    $this->addError($field, "Le champ {$field} doit être un nombre entier");
                }
                break;

            case 'float':
                if (!$this->security->validateFloat($value)) {
                    $this->addError($field, "Le champ {$field} doit être un nombre valide");
                }
                break;

            case 'min':
                if (strlen($value) < (int)$parameter) {
                    $this->addError($field, "Le champ {$field} doit contenir au moins {$parameter} caractères");
                }
                break;

            case 'max':
                if (strlen($value) > (int)$parameter) {
                    $this->addError($field, "Le champ {$field} ne doit pas dépasser {$parameter} caractères");
                }
                break;

            case 'numeric':
                if (!is_numeric($value)) {
                    $this->addError($field, "Le champ {$field} doit être numérique");
                }
                break;

            case 'alpha':
                if (!ctype_alpha($value)) {
                    $this->addError($field, "Le champ {$field} ne doit contenir que des lettres");
                }
                break;

            case 'alphanumeric':
                if (!ctype_alnum($value)) {
                    $this->addError($field, "Le champ {$field} ne doit contenir que des lettres et chiffres");
                }
                break;

            case 'confirmed':
                $confirmField = $field . '_confirmation';
                if ($value !== ($this->data[$confirmField] ?? null)) {
                    $this->addError($field, "La confirmation ne correspond pas");
                }
                break;

            case 'unique':
                // Format: unique:table,column
                if ($parameter) {
                    list($table, $column) = explode(',', $parameter);
                    if ($this->checkUnique($table, $column, $value)) {
                        $this->addError($field, "Cette valeur existe déjà");
                    }
                }
                break;
        }
    }

    /**
     * Ajoute une erreur
     */
    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    /**
     * Récupère toutes les erreurs
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Récupère les erreurs d'un champ spécifique
     */
    public function error(string $field): ?array
    {
        return $this->errors[$field] ?? null;
    }

    /**
     * Vérifie l'unicité en base de données
     */
    private function checkUnique(string $table, string $column, $value): bool
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT COUNT(*) FROM {$table} WHERE {$column} = ?");
            $stmt->execute([$value]);
            return $stmt->fetchColumn() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Nettoie les données selon les règles de sanitisation
     */
    public function sanitize(array $data, array $sanitizeRules = []): array
    {
        $sanitized = [];

        foreach ($data as $field => $value) {
            if (empty($sanitizeRules[$field])) {
                // Sanitisation par défaut
                $sanitized[$field] = $this->security->sanitize($value);
            } else {
                $rule = $sanitizeRules[$field];
                $sanitized[$field] = $this->applySanitization($value, $rule);
            }
        }

        return $sanitized;
    }

    /**
     * Applique une règle de sanitisation
     */
    private function applySanitization($value, string $rule)
    {
        switch ($rule) {
            case 'string':
                return $this->security->sanitize($value);
            case 'email':
                return $this->security->sanitizeEmail($value);
            case 'url':
                return $this->security->sanitizeUrl($value);
            case 'integer':
                return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
            case 'float':
                return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
            default:
                return $this->security->sanitize($value);
        }
    }

}
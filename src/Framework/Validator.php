<?php
namespace Framework;

use Framework\Database\Table;
use Framework\Validator\ValidationError;
use Psr\Http\Message\UploadedFileInterface;

class Validator
{


    private const MIME_TYPES = [
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'pdf' => 'application/pdf'
    ];


    /**
     * @var array
     */
    private $params;


    /**
     * @var string[]
     */
    private $errors = [];


    public function __construct(array $params)
    {
        $this->params = $params;
    }


    /**
     * Check the presence of fields in an array
     *
     * @param string[] ...$keys
     * @return Validator
     */
    public function required(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value)) {
                $this->addError($key, 'required');
            }
        }
        return $this;
    }


    /**
     * Check if the field is empty
     *
     * @param string[] ...$keys
     * @return Validator
     */
    public function notEmpty(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value) || empty($value)) {
                $this->addError($key, 'empty');
            }
        }
        return $this;
    }


    /**
     * Check the length of the field
     *
     * @param string $key
     * @param int|null $min
     * @param int|null $max
     * @return Validator
     */
    public function length(string $key, ?int $min, ?int $max = null): self
    {
        $value = $this->getValue($key);
        $length = mb_strlen($value);
        if (!is_null($min) &&
            !is_null($max) &&
            ($length < $min || $length > $max)
        ) {
            $this->addError($key, 'betweenLength', [$min, $max]);
            return $this;
        }
        if (!is_null($min) &&
            $length < $min
        ) {
            $this->addError($key, 'minLength', [$min]);
            return $this;
        }
        if (!is_null($max) &&
            $length > $max
        ) {
            $this->addError($key, 'maxLength', [$max]);
        }
        return $this;
    }


    /**
     * Check if the field is a valid slug
     *
     * @param string $key
     * @return Validator
     */
    public function slug(string $key): self
    {
        $value = $this->getValue($key);
        $pattern = '/^[a-z0-9]+(-[a-z0-9]+)*$/';
        if (!is_null($value) && !preg_match($pattern, $value)) {
            $this->addError($key, 'slug');
        }
        return $this;
    }


    /**
     * Check if the file was successfully uploaded
     *
     * @param string $key
     * @return Validator
     */
    public function uploaded(string $key): self
    {
        /** @var UploadedFileInterface $file */
        $file = $this->getValue($key);
        if ($file !== null || $file->getError() !== UPLOAD_ERR_OK) {
            $this->addError($key, 'uploaded');
        }
        return $this;
    }


    /**
     * Check the file's format
     *
     * @param string $key
     * @param array $extensions
     * @return Validator
     */
    public function extension(string $key, array $extensions): self
    {
        /** @var UploadedFileInterface $file */
        $file = $this->getValue($key);
        if ($file !== null && $file->getError() === UPLOAD_ERR_OK) {
            $type = $file->getClientMediaType();
            $extension = mb_strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
            $expectedType = self::MIME_TYPES[$extension] ?? null;
            if (!in_array($extension, $extensions) || $expectedType !== $type) {
                $this->addError($key, 'fileType', [join(', ', $extensions)]);
            }
        }
        return $this;
    }


    /**
     * Check the DateTime's format
     *
     * @param string $key
     * @param string $format
     * @return Validator
     */
    public function dateTime(string $key, string $format = 'Y-m-d H:i:s'): self
    {
        $value = $this->getValue($key);
        $date = \DateTime::createFromFormat($format, $value);
        $errors = \DateTime::getLastErrors();
        if ($errors['error_count'] > 0 || $errors['warning_count'] > 0 || $date === false) {
            $this->addError($key, 'datetime', [$format]);
        }
        return $this;
    }


    /**
     * Check if the key exist in a given table
     *
     * @param string $key
     * @param string $table
     * @param \PDO $pdo
     * @return Validator
     */
    public function exists(string $key, string $table, \PDO $pdo): self
    {
        $value = $this->getValue($key);
        $stmt = $pdo->prepare("SELECT id FROM $table WHERE id = ?");
        $stmt->execute([$value]);
        if ($stmt->fetchColumn() === false) {
            $this->addError($key, 'exists', [$table]);
        }
        return $this;
    }


    /**
     * Check if the key is unique in a given table
     *
     * @param string $key
     * @param string|Table $table
     * @param \PDO $pdo
     * @param int $exclude
     * @return Validator
     */
    public function unique(string $key, $table, ?\PDO $pdo = null, ?int $exclude = null): self
    {
        if ($table instanceof Table) {
            $pdo = $table->getPdo();
            $table = $table->getTable();
        }
        $value = $this->getValue($key);
        $query = "SELECT id FROM $table WHERE $key = ?";
        $params = [$value];
        if ($exclude !== null) {
            $query .= " AND id != ?";
            $params[] = $exclude;
        }
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        if ($stmt->fetchColumn() !== false) {
            $this->addError($key, 'unique', [$value]);
        }
        return $this;
    }


    /**
     * Check if the email is valid
     *
     * @param string $key
     * @return Validator
     */
    public function email(string $key): self
    {
        $value = $this->getValue($key);
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->addError($key, 'email');
        }
        return $this;
    }


    public function confirm(string $key): self
    {
        $value = $this->getValue($key);
        $valueConfirm = $this->getValue($key . '_confirm');
        if ($valueConfirm !== $value) {
            $this->addError($key, 'confirm');
        }
        return $this;
    }


    public function isValid(): bool
    {
        return empty($this->errors);
    }


    /**
     * Get the errors
     *
     * @return ValidationError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }


    /**
     * Add an error
     *
     * @param string $key
     * @param string $rule
     * @param array $attributes
     */
    private function addError(string $key, string $rule, array $attributes = []): void
    {
        $this->errors[$key] = new ValidationError($key, $rule, $attributes);
    }


    private function getValue(string $key)
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        return null;
    }
}

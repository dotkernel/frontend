<?php

declare(strict_types=1);

namespace Frontend\App\Service;

use Dot\AnnotatedServices\Annotation\Inject;
use Fig\Http\Message\StatusCodeInterface;
use InvalidArgumentException;

/**
 * Class RecaptchaService
 * @package Frontend\App\Service
 */
class RecaptchaService
{
    private array $config;
    private string $response;

    /**
     * RecaptchaService constructor.
     * @param array $config
     *
     * @Inject({
     *     "config.recaptcha"
     * })
     */
    public function __construct(array $config)
    {
        $this->validateConfig($config);

        $this->config = $config;
    }

    /**
     * @param string $response
     * @return $this
     */
    public function setResponse(string $response): self
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        if (empty($this->response)) {
            throw new InvalidArgumentException('Recaptcha response not initialized.');
        }

        $data = [
            'secret' => $this->config['secretKey'],
            'response' => $this->response,
        ];

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $this->config['verifyUrl']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));

        $response = curl_exec($curl);

        /** @psalm-suppress InvalidScalarArgument */
        $response = json_decode($response, true);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($statusCode !== StatusCodeInterface::STATUS_OK) {
            return false;
        }

        $success = $response['success'] ?? false;
        $score = $response['score'] ?? 0;

        return $success && $score > $this->config['scoreThreshold'];
    }

    /**
     * @param array $config
     * @return void
     */
    private function validateConfig(array $config): void
    {
        $keysToValidate = ['siteKey', 'secretKey', 'verifyUrl', 'scoreThreshold'];
        foreach ($keysToValidate as $key) {
            if (empty($config[$key])) {
                throw new InvalidArgumentException(
                    sprintf('Invalid `%s` provided.', $key)
                );
            }
        }

        if (! is_float($config['scoreThreshold']) || $config['scoreThreshold'] < 0 || $config['scoreThreshold'] > 1) {
            throw new InvalidArgumentException(
                sprintf('Invalid `%s` provided. The value must be a float between 0.0 and 1.0', $key)
            );
        }
    }
}

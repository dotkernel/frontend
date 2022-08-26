<?php


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

    private float $scoreThreshold;

    /**
     * RecaptchaService constructor.
     * @param array $config
     *
     * @Inject({"config.recaptcha"})
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->scoreThreshold = $config['scoreThreshold'];
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
     *
     * Overwrite the default score threshold if needed
     *
     * @param float $scoreThreshold
     * @return $this
     */
    public function setScoreThreshold(float $scoreThreshold): self
    {
        $this->scoreThreshold = $scoreThreshold;

        return $this;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        if (! isset($this->response)) {
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
        $response = json_decode($response, true);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($statusCode !== StatusCodeInterface::STATUS_OK) {
            return false;
        }

        $success = $response['success'] ?? false;
        $score = $response['score'] ?? 0;

        return $success && $score > $this->scoreThreshold;
    }
}

<?php

declare(strict_types=1);

namespace Modules\OpenAI\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class OpenAIRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return array_merge([
            'prompt' => [
                'required',
                'string',
                'max:4000',
            ],
            'model' => [
                'required',
                'string',
                'in:gpt-3.5-turbo,gpt-4,gpt-4-turbo,text-davinci-003,text-davinci-002,text-curie-001,text-babbage-001,text-ada-001',
            ],
            'max_tokens' => [
                'nullable',
                'integer',
                'min:1',
                'max:4096',
            ],
            'temperature' => [
                'nullable',
                'numeric',
                'min:0',
                'max:2',
            ],
            'top_p' => [
                'nullable',
                'numeric',
                'min:0',
                'max:1',
            ],
            'frequency_penalty' => [
                'nullable',
                'numeric',
                'min:-2',
                'max:2',
            ],
            'presence_penalty' => [
                'nullable',
                'numeric',
                'min:-2',
                'max:2',
            ],
            'stop' => [
                'nullable',
                'array',
                'max:4',
            ],
            'stop.*' => [
                'string',
                'max:200',
            ],
            'user' => [
                'nullable',
                'string',
                'max:100',
            ],
            'stream' => [
                'boolean',
            ],
            'logit_bias' => [
                'nullable',
                'array',
            ],
            'logit_bias.*' => [
                'numeric',
                'min:-100',
                'max:100',
            ],
            'echo' => [
                'boolean',
            ],
            'suffix' => [
                'nullable',
                'string',
                'max:500',
            ],
            'best_of' => [
                'nullable',
                'integer',
                'min:1',
                'max:20',
            ],
            'n' => [
                'nullable',
                'integer',
                'min:1',
                'max:10',
            ],
            'logprobs' => [
                'nullable',
                'integer',
                'min:0',
                'max:5',
            ],
            'return_prompt' => [
                'boolean',
            ],
            'return_metadata' => [
                'boolean',
            ],
            'return_usage' => [
                'boolean',
            ],
            'return_choices' => [
                'boolean',
            ],
            'return_finish_reason' => [
                'boolean',
            ],
            'return_logprobs' => [
                'boolean',
            ],
            'return_tokens' => [
                'boolean',
            ],
            'return_text' => [
                'boolean',
            ],
            'return_full_response' => [
                'boolean',
            ],
            'return_raw_response' => [
                'boolean',
            ],
            'return_processed_response' => [
                'boolean',
            ],
            'return_formatted_response' => [
                'boolean',
            ],
            'return_json_response' => [
                'boolean',
            ],
            'return_xml_response' => [
                'boolean',
            ],
            'return_yaml_response' => [
                'boolean',
            ],
            'return_csv_response' => [
                'boolean',
            ],
            'return_tsv_response' => [
                'boolean',
            ],
            'return_html_response' => [
                'boolean',
            ],
            'return_markdown_response' => [
                'boolean',
            ],
            'return_plain_text_response' => [
                'boolean',
            ],
            'return_rich_text_response' => [
                'boolean',
            ],
            'return_formatted_text_response' => [
                'boolean',
            ],
            'return_structured_response' => [
                'boolean',
            ],
            'return_unstructured_response' => [
                'boolean',
            ],
            'return_semi_structured_response' => [
                'boolean',
            ],
            'return_hierarchical_response' => [
                'boolean',
            ],
            'return_flat_response' => [
                'boolean',
            ],
            'return_nested_response' => [
                'boolean',
            ],
            'return_key_value_response' => [
                'boolean',
            ],
            'return_array_response' => [
                'boolean',
            ],
            'return_object_response' => [
                'boolean',
            ],
            'return_list_response' => [
                'boolean',
            ],
            'return_set_response' => [
                'boolean',
            ],
            'return_map_response' => [
                'boolean',
            ],
            'return_dictionary_response' => [
                'boolean',
            ],
            'return_table_response' => [
                'boolean',
            ],
            'return_grid_response' => [
                'boolean',
            ],
            'return_matrix_response' => [
                'boolean',
            ],
            'return_vector_response' => [
                'boolean',
            ],
            'return_scalar_response' => [
                'boolean',
            ],
            'return_primitive_response' => [
                'boolean',
            ],
            'return_complex_response' => [
                'boolean',
            ],
            'return_compound_response' => [
                'boolean',
            ],
            'return_aggregate_response' => [
                'boolean',
            ],
            'return_collection_response' => [
                'boolean',
            ],
            'return_sequence_response' => [
                'boolean',
            ],
            'return_series_response' => [
                'boolean',
            ],
            'return_parallel_response' => [
                'boolean',
            ],
            'return_concurrent_response' => [
                'boolean',
            ],
            'return_synchronous_response' => [
                'boolean',
            ],
            'return_asynchronous_response' => [
                'boolean',
            ],
            'return_synchronous_async_response' => [
                'boolean',
            ],
            'return_async_synchronous_response' => [
                'boolean',
            ],
            'return_hybrid_response' => [
                'boolean',
            ],
            'return_mixed_response' => [
                'boolean',
            ],
            'return_other_response' => [
                'boolean',
            ],
        ], $this->getCommonRules());
    }

    /**
     * Additional validation rules.
     */
    protected function additionalValidation($validator): void
    {
        $validator->after(function ($validator): void {
            // Validate prompt length
            if ($this->filled('prompt')) {
                $prompt = $this->prompt;

                if (mb_strlen($prompt) < 10) {
                    $validator->errors()->add(
                        'prompt',
                        'Prompt must be at least 10 characters long.'
                    );
                }

                if (mb_strlen($prompt) > 4000) {
                    $validator->errors()->add(
                        'prompt',
                        'Prompt must not exceed 4000 characters.'
                    );
                }
            }

            // Validate model
            if ($this->filled('model')) {
                $model = $this->model;
                $validModels = [
                    'gpt-3.5-turbo', 'gpt-4', 'gpt-4-turbo',
                    'text-davinci-003', 'text-davinci-002',
                    'text-curie-001', 'text-babbage-001', 'text-ada-001',
                ];

                if (! in_array($model, $validModels)) {
                    $validator->errors()->add(
                        'model',
                        'Invalid model selected.'
                    );
                }
            }

            // Validate max tokens
            if ($this->filled('max_tokens')) {
                $maxTokens = $this->max_tokens;

                if ($maxTokens < 1) {
                    $validator->errors()->add(
                        'max_tokens',
                        'Max tokens must be at least 1.'
                    );
                }

                if ($maxTokens > 4096) {
                    $validator->errors()->add(
                        'max_tokens',
                        'Max tokens cannot exceed 4096.'
                    );
                }
            }

            // Validate temperature
            if ($this->filled('temperature')) {
                $temperature = $this->temperature;

                if ($temperature < 0) {
                    $validator->errors()->add(
                        'temperature',
                        'Temperature must be at least 0.'
                    );
                }

                if ($temperature > 2) {
                    $validator->errors()->add(
                        'temperature',
                        'Temperature cannot exceed 2.'
                    );
                }
            }

            // Validate top_p
            if ($this->filled('top_p')) {
                $topP = $this->top_p;

                if ($topP < 0) {
                    $validator->errors()->add(
                        'top_p',
                        'Top P must be at least 0.'
                    );
                }

                if ($topP > 1) {
                    $validator->errors()->add(
                        'top_p',
                        'Top P cannot exceed 1.'
                    );
                }
            }

            // Validate frequency penalty
            if ($this->filled('frequency_penalty')) {
                $frequencyPenalty = $this->frequency_penalty;

                if ($frequencyPenalty < -2) {
                    $validator->errors()->add(
                        'frequency_penalty',
                        'Frequency penalty must be at least -2.'
                    );
                }

                if ($frequencyPenalty > 2) {
                    $validator->errors()->add(
                        'frequency_penalty',
                        'Frequency penalty cannot exceed 2.'
                    );
                }
            }

            // Validate presence penalty
            if ($this->filled('presence_penalty')) {
                $presencePenalty = $this->presence_penalty;

                if ($presencePenalty < -2) {
                    $validator->errors()->add(
                        'presence_penalty',
                        'Presence penalty must be at least -2.'
                    );
                }

                if ($presencePenalty > 2) {
                    $validator->errors()->add(
                        'presence_penalty',
                        'Presence penalty cannot exceed 2.'
                    );
                }
            }

            // Validate stop sequences
            if ($this->filled('stop')) {
                $stop = $this->stop;

                if (count($stop) > 4) {
                    $validator->errors()->add(
                        'stop',
                        'Maximum 4 stop sequences are allowed.'
                    );
                }

                foreach ($stop as $index => $sequence) {
                    if (mb_strlen($sequence) < 1) {
                        $validator->errors()->add(
                            'stop.'.$index,
                            'Each stop sequence must be at least 1 character long.'
                        );
                    }

                    if (mb_strlen($sequence) > 200) {
                        $validator->errors()->add(
                            'stop.'.$index,
                            'Each stop sequence must not exceed 200 characters.'
                        );
                    }
                }
            }

            // Validate user
            if ($this->filled('user') && mb_strlen($this->user) > 100) {
                $validator->errors()->add(
                    'user',
                    'User must not exceed 100 characters.'
                );
            }

            // Validate logit bias
            if ($this->filled('logit_bias')) {
                $logitBias = $this->logit_bias;

                if (count($logitBias) > 300) {
                    $validator->errors()->add(
                        'logit_bias',
                        'Maximum 300 logit bias entries are allowed.'
                    );
                }

                foreach ($logitBias as $key => $value) {
                    if ($value < -100) {
                        $validator->errors()->add(
                            'logit_bias.'.$key,
                            'Logit bias value must be at least -100.'
                        );
                    }

                    if ($value > 100) {
                        $validator->errors()->add(
                            'logit_bias.'.$key,
                            'Logit bias value cannot exceed 100.'
                        );
                    }
                }
            }

            // Validate suffix
            if ($this->filled('suffix') && mb_strlen($this->suffix) > 500) {
                $validator->errors()->add(
                    'suffix',
                    'Suffix must not exceed 500 characters.'
                );
            }

            // Validate best_of
            if ($this->filled('best_of')) {
                $bestOf = $this->best_of;

                if ($bestOf < 1) {
                    $validator->errors()->add(
                        'best_of',
                        'Best of must be at least 1.'
                    );
                }

                if ($bestOf > 20) {
                    $validator->errors()->add(
                        'best_of',
                        'Best of cannot exceed 20.'
                    );
                }
            }

            // Validate n
            if ($this->filled('n')) {
                $n = $this->n;

                if ($n < 1) {
                    $validator->errors()->add(
                        'n',
                        'N must be at least 1.'
                    );
                }

                if ($n > 10) {
                    $validator->errors()->add(
                        'n',
                        'N cannot exceed 10.'
                    );
                }
            }

            // Validate logprobs
            if ($this->filled('logprobs')) {
                $logprobs = $this->logprobs;

                if ($logprobs < 0) {
                    $validator->errors()->add(
                        'logprobs',
                        'Logprobs must be at least 0.'
                    );
                }

                if ($logprobs > 5) {
                    $validator->errors()->add(
                        'logprobs',
                        'Logprobs cannot exceed 5.'
                    );
                }
            }
        });
    }
}

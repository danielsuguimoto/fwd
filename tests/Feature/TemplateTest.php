<?php

namespace Tests\Feature;

use App\Environment;
use Tests\TestCase;
use Illuminate\Support\Facades\File;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;

class TemplateTest extends TestCase
{
    public function testTemplate()
    {
        $this->mock(Environment::class, function ($mock) {
            $mock->shouldReceive('getContextFile')
                ->once()
                ->with('fwd-template.json')
                ->andReturn('fwd-template.json');
        })->makePartial();

        File::shouldReceive('get')
            ->once()
            ->with('fwd-template.json')
            ->andReturn(file_get_contents('tests/fixtures/template/fwd-template.json'));

        $path = './folder1';

        File::shouldReceive('isDirectory')
            ->once()
            ->with($path)
            ->andReturn(true);

        File::shouldReceive('deleteDirectory')
            ->once()
            ->with($path)
            ->andReturn(true);

        File::shouldReceive('makeDirectory')
            ->once()
            ->with($path, 0755, true)
            ->andReturn(true);

        $viewMock = $this->mock(View::class, function ($mock) {
            $mock->shouldReceive('render')
                ->twice()
                ->andReturn('dockerfile', 'entrypoint');
        })->makePartial();

        $this->mock(ViewFactory::class, function ($mock) use ($viewMock) {
            $mock->shouldReceive('make')
                ->twice()
                ->withArgs(function ($view, $viewData, $mergedData) {
                    if (! in_array($view, [
                        'template/Dockerfile',
                        'template/entrypoint',
                    ])) {
                        return false;
                    }

                    return $viewData === [
                        'from' => 'image1',
                        'version' => 'version1',
                    ] && $mergedData === [];
                })
                ->andReturn($viewMock);
        })->makePartial();

        File::shouldReceive('put')
            ->twice()
            ->withArgs(function ($path, $content) {
                if ($path === './folder1/Dockerfile') {
                    return $content === 'dockerfile';
                }

                if ($path === './folder1/entrypoint') {
                    return $content === 'entrypoint';
                }

                return false;
            })
            ->andReturn(1);

        $this->artisan('template')
            ->assertExitCode(0)
            ->expectsOutput('File [./folder1/Dockerfile] generated.')
            ->expectsOutput('File [./folder1/entrypoint] generated.')
            ->expectsOutput('Templates generated successfully.');
    }
}

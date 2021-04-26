<?php

declare( strict_types = 1 );

namespace EDTF\Tests\Unit\PackagePrivate\Humanizer\Internationalization;

use EDTF\PackagePrivate\Humanizer\Internationalization\ArrayMessageBuilder;
use EDTF\PackagePrivate\Humanizer\Internationalization\FallbackMessageBuilder;
use EDTF\PackagePrivate\Humanizer\Internationalization\UnknownMessageKey;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EDTF\PackagePrivate\Humanizer\Internationalization\FallbackMessageBuilder
 */
class FallbackMessageBuilderTest extends TestCase {

	public function testBuildMessageThrowsException(): void {
		$builder = new FallbackMessageBuilder( new ArrayMessageBuilder( [] ), new ArrayMessageBuilder( [] ) );
		$this->expectException( UnknownMessageKey::class );

		$builder->buildMessage( 'not-existing-key' );
	}

	public function testBuildMessageUseFallbackTranslation(): void {
		$primaryBuilder = new ArrayMessageBuilder( [] );
		$fallbackBuilder = new ArrayMessageBuilder( [ 'random-string' => 'Random string' ] );

		$builder = new FallbackMessageBuilder( $primaryBuilder, $fallbackBuilder );

		$this->assertSame( "Random string", $builder->buildMessage( 'random-string' ) );
	}

	public function testBuildMessageUsePrimaryTranslation(): void {
		$primaryBuilder = new ArrayMessageBuilder( [ 'random-string' => 'Random string' ] );

		$fallbackBuilderSpy = $this->createMock( ArrayMessageBuilder::class );
		$fallbackBuilderSpy
			->expects( $this->never() )
			->method( 'buildMessage' );

		$builder = new FallbackMessageBuilder( $primaryBuilder, $fallbackBuilderSpy );
		$this->assertSame( "Random string", $builder->buildMessage( 'random-string' ) );
	}
}
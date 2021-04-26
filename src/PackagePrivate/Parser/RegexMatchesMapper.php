<?php

namespace EDTF\PackagePrivate\Parser;

class RegexMatchesMapper {

	public function mapMatchesToObject( array $matches ): ParsedData {
		$regrouped = $this->regroupMatches( $matches );

		return new ParsedData(
			$this->mapDate( $regrouped['date'] ),
			$this->mapTime( $regrouped['time'] ),
			$this->mapQualification( $regrouped['qualification'] ),
			$this->mapTimeZone( $regrouped['tz'] )
		);
	}

	private function matchesGroupMap(): array {
		return [
			'date' => [ 'year', 'month', 'day', 'yearNum', 'monthNum', 'dayNum', 'yearSignificantDigit' ],
			'time' => [ 'hour', 'minute', 'second' ],
			'tz' => [ 'tzHour', 'tzMinute', 'tzSign', 'tzUtc' ],
			'qualification' => [
				'yearOpenFlag',
				'monthOpenFlag',
				'dayOpenFlag',
				'yearCloseFlag',
				'monthCloseFlag',
				'dayCloseFlag' ],
		];
	}

	private function mapDate( array $rawDateMatches ): Date {
		return new Date(
			$rawDateMatches['year'] ?? null,
			$rawDateMatches['month'] ?? null,
			$rawDateMatches['day'] ?? null,
			!empty( $rawDateMatches['yearNum'] ) ? $this->prepareNumValue( $rawDateMatches['yearNum'] ) : null,
			!empty( $rawDateMatches['monthNum'] ) ? $this->prepareNumValue( $rawDateMatches['monthNum'] ) : null,
			!empty( $rawDateMatches['dayNum'] ) ? $this->prepareNumValue( $rawDateMatches['dayNum'] ) : null,
			!empty( $rawDateMatches['yearSignificantDigit'] ) ? (int)$rawDateMatches['yearSignificantDigit'] : null
		);
	}

	private function prepareNumValue( string $str ): ?int {
		$value = (int)str_replace( 'X', '0', $str );
		return $value !== 0 ? $value : null;
	}

	private function mapTime( array $matches ): Time {
		return new Time(
			!empty( $matches['hour'] ) ? (int)$matches['hour'] : null,
			!empty( $matches['minute'] ) ? (int)$matches['minute'] : null,
			!empty( $matches['second'] ) ? (int)$matches['second'] : null,
		);
	}

	private function mapTimezone( array $matches ): Timezone {
		return new Timezone(
			!empty( $matches['tzHour'] ) ? (int)$matches['tzHour'] : null,
			!empty( $matches['tzMinute'] ) ? (int)$matches['tzMinute'] : null,
			$matches['tzSign'] ?? null,
			$matches['tzUtc'] ?? null
		);
	}

	private function mapQualification( array $matches ): Qualification {
		return new Qualification(
			!empty( $matches['yearOpenFlag'] ) ? $matches['yearOpenFlag'] : null,
			!empty( $matches['monthOpenFlag'] ) ? $matches['monthOpenFlag'] : null,
			!empty( $matches['dayOpenFlag'] ) ? $matches['dayOpenFlag'] : null,
			!empty( $matches['yearCloseFlag'] ) ? $matches['yearCloseFlag'] : null,
			!empty( $matches['monthCloseFlag'] ) ? $matches['monthCloseFlag'] : null,
			!empty( $matches['dayCloseFlag'] ) ? $matches['dayCloseFlag'] : null
		);
	}

	private function regroupMatches( array $matches ): array {
		$regrouped = [ 'date' => [], 'time' => [], 'tz' => [], 'qualification' => [] ];

		foreach ( $matches as $name => $value ) {
			foreach ( $this->matchesGroupMap() as $group => $keyList ) {
				if ( in_array( $name, $keyList ) ) {
					$regrouped[$group][$name] = $value;
					break;
				}
			}
		}

		return $regrouped;
	}
}
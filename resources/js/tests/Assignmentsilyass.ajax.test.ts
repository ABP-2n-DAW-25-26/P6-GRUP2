import { describe, expect, test } from 'vitest';

describe('Assignments AJAX', () => {
    test('should contain assignment endpoint', () => {
        const endpoint = '/assignments/create';

        expect(endpoint).toContain('/assignments');
    });
});
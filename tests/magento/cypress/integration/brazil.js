/* global Cypress, it, describe, before, context, cy */

import {
  assertUrlStatus,
} from '../../../utils';

context('Brazil', () => {
  it('should visit', () => {
    assertUrlStatus(Cypress.env('DEMO_URL'));
  });
});

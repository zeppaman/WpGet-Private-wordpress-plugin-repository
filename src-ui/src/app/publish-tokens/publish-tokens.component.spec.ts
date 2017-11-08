import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { PublishTokensComponent } from './publish-tokens.component';

describe('PublishTokensComponent', () => {
  let component: PublishTokensComponent;
  let fixture: ComponentFixture<PublishTokensComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ PublishTokensComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PublishTokensComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

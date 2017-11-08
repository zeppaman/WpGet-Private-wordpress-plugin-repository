import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { UploadPackageComponent } from './upload-package.component';

describe('UploadPackageComponent', () => {
  let component: UploadPackageComponent;
  let fixture: ComponentFixture<UploadPackageComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ UploadPackageComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(UploadPackageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

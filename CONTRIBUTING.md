# Contributing

Issues and pull requests are welcome. Keep changes focused and preserve the
package's public interface unless a breaking change is intentional.

Before opening a pull request, run:

```bash
composer validate --strict
composer lint
```

Every change to the installer or generator must include a test that exercises
the public command or the internal seam responsible for the behaviour. Update
the README and CHANGELOG when installation behaviour changes.

The package follows Semantic Versioning. Do not introduce a new Laravel major
support range without updating the compatibility matrix and CI workflow.
